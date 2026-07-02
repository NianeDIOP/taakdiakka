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

    public function showRegister(Request $request)
    {
        if (! \App\Models\Setting::enabled('site.registration_open', true)) {
            return view('auth.register-closed');
        }

        // Persiste le code parrain en session pour survivre au POST
        if ($request->filled('ref')) {
            $request->session()->put('referral_code', $request->input('ref'));
        }

        return view('auth.register', [
            'refCode' => $request->session()->get('referral_code', $request->input('ref', '')),
        ]);
    }

    public function register(Request $request)
    {
        abort_unless(\App\Models\Setting::enabled('site.registration_open', true), 403, 'Les inscriptions sont actuellement fermées.');

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', Password::min(8)],
            'referral_code'=> ['nullable', 'string', 'max:10'],
        ], [], [
            'name' => 'nom', 'email' => 'adresse e-mail', 'password' => 'mot de passe',
        ]);

        // Résolution du parrain
        $refCode  = $data['referral_code'] ?? $request->session()->pull('referral_code');
        $referrer = $refCode ? User::where('referral_code', strtoupper(trim($refCode)))->first() : null;

        $signupBonus = (int) \App\Models\Setting::get('referral_signup_bonus', 30);

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'referral_code' => User::generateReferralCode(),
            'referred_by'   => $referrer?->id,
        ]);

        // Bonus pièces pour le nouveau filleul
        if ($referrer && $signupBonus > 0) {
            $newBalance = $user->coins_balance + $signupBonus;
            $user->update(['coins_balance' => $newBalance]);
            \App\Models\CoinTransaction::create([
                'user_id'      => $user->id,
                'type'         => 'referral_signup',
                'coins'        => $signupBonus,
                'balance_after'=> $newBalance,
                'description'  => "Bonus parrainage — parrainé par {$referrer->name}",
            ]);
        }

        // E-mail de bienvenue (n'interrompt jamais l'inscription en cas d'échec d'envoi)
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $statusMsg = $referrer
            ? "Bienvenue ! Vous avez reçu 🪙 {$signupBonus} pièces grâce au parrainage. Complétons votre profil. 🤲"
            : 'Bienvenue ! Complétons votre profil en 3 étapes. 🤲';

        return redirect()->route('onboarding')->with('status', $statusMsg);
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
