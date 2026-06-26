<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class MemberController extends Controller
{
    public function messages()
    {
        return view('espace.messages');
    }

    public function favoris()
    {
        $me = auth()->user();

        // Favoris (stockés sur les demandes) → on affiche les MEMBRES correspondants
        $members = $me->favorites()
            ->with(['user.profile', 'user.demandes:id,user_id,status'])
            ->latest('favorites.created_at')
            ->get()
            ->map(fn ($d) => $d->user)
            ->filter()
            ->unique('id')
            ->values();

        // Suggestions : membres du genre recherché (jamais le même genre)
        $sought = match ($me->profile?->gender) {
            'Homme' => 'Femme',
            'Femme' => 'Homme',
            default => null,
        };
        $sugQuery = User::whereKeyNot($me->id)
            ->whereHas('profile', fn ($q) => $q->whereNotNull('gender'))
            ->whereDoesntHave('demandes', fn ($q) => $q->where('status', 'suspended'))
            ->with(['profile', 'demandes:id,user_id,status']);
        if ($sought) {
            $sugQuery->whereHas('profile', fn ($q) => $q->where('gender', $sought));
        }
        $suggestions = $sugQuery->latest('id')->take(6)->get();

        return view('espace.favoris', compact('members', 'suggestions'));
    }

    /** Ajoute / retire une demande des favoris. */
    public function toggleFavorite(Demande $demande)
    {
        auth()->user()->favorites()->toggle($demande->id);

        return back();
    }

    public function verification()
    {
        $profile = auth()->user()->profileOrNew();

        return view('espace.verification', [
            'current'      => $profile->verification_level ?: 'Bronze',
            'phone'        => $profile->phone,
            'phoneOk'      => (bool) $profile->phone,
        ]);
    }

    /** Traite une étape de vérification (téléphone, pièce d'identité, selfie). */
    public function verify(Request $request)
    {
        $profile = auth()->user()->profileOrNew();

        $step = $request->input('step');

        if ($step === 'phone') {
            $data = $request->validate([
                'phone' => ['required', 'string', 'max:30'],
            ], [], ['phone' => 'numéro de téléphone']);
            $profile->update(['phone' => $data['phone']]);

            \App\Support\Notifier::email(auth()->user(), 'Niveau Bronze validé ✅', 'Votre vérification progresse',
                ['Votre numéro de téléphone est confirmé : votre profil affiche désormais le badge Bronze.', 'Continuez la vérification (pièce d\'identité, selfie) pour gagner la confiance des membres.'],
                'Gérer ma vérification', route('verification'));

            return back()->with('status', 'Numéro de téléphone confirmé. Niveau Bronze validé ✅');
        }

        if (in_array($step, ['Argent', 'Or'], true)) {
            // Le palier Or exige d'avoir atteint Argent
            if ($step === 'Or' && (Profile::VERIF_RANK[$profile->verification_level] ?? 1) < 2) {
                return back()->with('status', 'Validez d\'abord le niveau Argent (pièce d\'identité).');
            }

            $request->validate([
                'document' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            ], [], ['document' => $step === 'Or' ? 'selfie' : 'pièce d\'identité']);

            // Démo : approbation immédiate (en production : file d'attente de modération)
            $profile->update(['verification_level' => $step]);
            \App\Models\Demande::activateFor(auth()->user()->fresh()); // synchronise le badge

            \App\Support\Notifier::email(auth()->user(), "Niveau {$step} validé 🎖️", "Félicitations, vous êtes vérifié(e) {$step}",
                ["Votre niveau de vérification {$step} est validé : votre profil affiche désormais le badge correspondant.", 'Les profils vérifiés inspirent davantage confiance et reçoivent plus de visites. 🤲'],
                'Voir mon profil', route('profile.show'));

            return back()->with('status', "Félicitations — niveau {$step} validé ! Votre badge est mis à jour. 🎖️");
        }

        return back();
    }

    public function settings()
    {
        return view('espace.parametres', [
            'demande' => auth()->user()->demandes()->first(),
        ]);
    }

    /** « Mon abonnement » : formule en cours, limites de la version gratuite, mise à niveau. */
    public function subscription()
    {
        $user = auth()->user();
        $gate = \App\Support\FeatureGate::class;

        return view('espace.abonnement', [
            'user'        => $user,
            'sub'         => $user->activeSubscription(),
            'isPremium'   => $gate::isPremium($user),
            'enforced'    => $gate::monetizationEnabled(),
            'plans'       => \App\Models\Plan::active()->orderBy('sort_order')->get(),
            'limits'      => [
                'friend'   => $gate::canSendFriendRequest($user),
                'messages' => $gate::messagesPerContact($user),
                'photos'   => $gate::visiblePhotos($user),
                'visitors' => $gate::canSeeVisitors($user),
            ],
            'verifLevel'  => $user->profile?->verification_level ?? 'Bronze',
        ]);
    }

    /** Préférence : recevoir ou non les e-mails de notification. */
    public function updateNotifications(Request $request)
    {
        $request->user()->update(['email_opt_in' => $request->boolean('email_opt_in')]);

        return back()->with('status', $request->boolean('email_opt_in')
            ? 'Vous recevrez désormais les notifications par e-mail.'
            : 'Notifications par e-mail désactivées.');
    }

    /** Confidentialité : masquer/afficher la photo (profil discret). */
    public function updatePrivacy(Request $request)
    {
        $demande = auth()->user()->demandes()->first();
        if (! $demande) {
            return back()->with('status', 'Activez d\'abord votre demande pour gérer sa confidentialité.');
        }

        $demande->update(['is_discret' => $request->boolean('is_discret')]);

        return back()->with('status', $request->boolean('is_discret')
            ? 'Votre photo est désormais masquée (profil discret).'
            : 'Votre photo est de nouveau visible.');
    }

    /** Suppression définitive du compte. */
    public function destroyAccount(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ], [], ['current_password' => 'mot de passe']);

        $user = $request->user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect()->route('home')->with('status', 'Votre compte a été supprimé. Qu\'Allah vous accompagne. 🤲');
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [], ['name' => 'nom', 'email' => 'adresse e-mail']);

        $user->update($data);

        return back()->with('status', 'Vos informations de compte ont été mises à jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'current_password' => 'mot de passe actuel', 'password' => 'nouveau mot de passe',
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Votre mot de passe a été modifié.');
    }
}
