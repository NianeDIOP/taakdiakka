<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /* ---------- Inscription ---------- */

    public function showRegister()
    {
        if (! \App\Models\Setting::enabled('site.registration_open', true)) {
            return view('auth.register-closed');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        abort_unless(\App\Models\Setting::enabled('site.registration_open', true), 403, 'Les inscriptions sont actuellement fermées.');

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'name' => 'nom', 'email' => 'adresse e-mail', 'password' => 'mot de passe',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // E-mail de bienvenue (n'interrompt jamais l'inscription en cas d'échec d'envoi)
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Nouveau membre → parcours d'accueil guidé
        return redirect()->route('onboarding')->with('status', 'Bienvenue ! Complétons votre profil en 3 étapes. 🤲');
    }

    /* ---------- Connexion ---------- */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email' => 'adresse e-mail', 'password' => 'mot de passe',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects. Veuillez réessayer.'])
                ->onlyInput('email');
        }

        $user = Auth::user();
        if ($user->isBlocked()) {
            $reason = $user->status === 'banned'
                ? 'Votre compte a été banni.'
                : 'Votre compte est suspendu' . ($user->status_reason ? ' : ' . $user->status_reason : '') . '.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => $reason])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // Les comptes admin / modérateur atterrissent dans le back-office.
        $home = $user->isAdminUser() ? route('admin.dashboard') : route('dashboard');

        return redirect()->intended($home);
    }

    /* ---------- Déconnexion ---------- */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
