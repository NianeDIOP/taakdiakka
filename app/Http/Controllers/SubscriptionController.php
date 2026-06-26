<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Support\Payments\PaymentManager;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /** Page publique des tarifs (plans dynamiques). */
    public function tarifs()
    {
        $plans = Plan::active()->orderBy('sort_order')->get();
        $boosts = \App\Models\BoostPack::active()->orderBy('sort_order')->get();
        $current = auth()->check() ? auth()->user()->activeSubscription() : null;

        return view('tarifs', compact('plans', 'boosts', 'current'));
    }

    /** Lance la souscription à un plan : crée une intention puis redirige vers le paiement. */
    public function checkout(Request $request, Plan $plan)
    {
        $user = $request->user();

        if (! $plan->is_active) {
            return redirect()->route('tarifs')->with('status', 'Cette formule n\'est plus disponible.');
        }

        // Plan gratuit : rien à payer
        if ($plan->is_free) {
            return redirect()->route('tarifs')->with('status', 'La formule Découverte est déjà active par défaut. 🌙');
        }

        // Réutilise une intention en attente pour ce plan, ou en crée une
        $subscription = Subscription::create([
            'user_id'          => $user->id,
            'plan_id'          => $plan->id,
            'status'           => 'pending',
            'amount'           => $plan->price,
            'payment_provider' => PaymentManager::gateway()->key(),
        ]);

        $result = PaymentManager::gateway()->initiate(
            ['amount' => $plan->price, 'label' => 'Abonnement ' . $plan->name, 'email' => $user->email],
            route('subscribe.callback', ['subscription' => $subscription->id]),
            route('tarifs'),
        );

        if (! $result->ok) {
            $subscription->update(['status' => 'cancelled']);

            return redirect()->route('tarifs')->with('status', 'Paiement indisponible : ' . $result->error);
        }

        $subscription->update(['payment_reference' => $result->reference]);

        return redirect()->away($result->redirectUrl);
    }

    /** Page de paiement factice (mode démonstration uniquement). */
    public function simulate(Request $request)
    {
        return view('abonnement.simulate', [
            'ref'    => $request->query('ref'),
            'return' => $request->query('return'),
            'cancel' => $request->query('cancel'),
            'amount' => (int) $request->query('amount'),
            'label'  => $request->query('label'),
        ]);
    }

    /** Retour du prestataire : confirme le paiement et active l'abonnement. */
    public function callback(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->user_id === auth()->id(), 403);

        if ($subscription->status === 'active') {
            return redirect()->route('subscribe.success', $subscription);
        }

        $confirmed = PaymentManager::gateway()->confirm($subscription->payment_reference ?? '');

        if (! $confirmed) {
            $subscription->update(['status' => 'cancelled']);

            return redirect()->route('tarifs')->with('status', 'Le paiement n\'a pas pu être confirmé.');
        }

        $plan = $subscription->plan;
        $subscription->update([
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
        ]);

        // Désactive les anciens abonnements actifs
        Subscription::where('user_id', $subscription->user_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        return redirect()->route('subscribe.success', $subscription);
    }

    /** Confirmation de souscription. */
    public function success(Subscription $subscription)
    {
        abort_unless($subscription->user_id === auth()->id(), 403);

        return view('abonnement.success', compact('subscription'));
    }

    /* ---------------- Boosts ---------------- */

    /** Achète un boost de visibilité. */
    public function boostCheckout(Request $request, \App\Models\BoostPack $boostPack)
    {
        $user = $request->user();

        if (! $boostPack->is_active) {
            return redirect()->route('tarifs')->with('status', 'Ce boost n\'est plus disponible.');
        }

        $boost = \App\Models\Boost::create([
            'user_id'       => $user->id,
            'boost_pack_id' => $boostPack->id,
            'amount'        => $boostPack->price,
        ]);

        $result = PaymentManager::gateway()->initiate(
            ['amount' => $boostPack->price, 'label' => 'Boost ' . $boostPack->name, 'email' => $user->email],
            route('boost.callback', ['boost' => $boost->id]),
            route('tarifs'),
        );

        if (! $result->ok) {
            $boost->delete();

            return redirect()->route('tarifs')->with('status', 'Paiement indisponible : ' . $result->error);
        }

        $boost->update(['payment_reference' => $result->reference]);

        return redirect()->away($result->redirectUrl);
    }

    /** Retour du prestataire pour un boost. */
    public function boostCallback(\App\Models\Boost $boost)
    {
        abort_unless($boost->user_id === auth()->id(), 403);

        if ($boost->ends_at && $boost->ends_at->isFuture()) {
            return redirect()->route('dashboard')->with('status', 'Votre profil est déjà mis en avant ✨');
        }

        $confirmed = PaymentManager::gateway()->confirm($boost->payment_reference ?? '');

        if (! $confirmed) {
            $boost->delete();

            return redirect()->route('tarifs')->with('status', 'Le paiement du boost n\'a pas pu être confirmé.');
        }

        $boost->update([
            'starts_at' => now(),
            'ends_at'   => now()->addDays($boost->pack?->duration_days ?? 1),
        ]);

        return redirect()->route('dashboard')->with('status', 'Votre profil est désormais mis en avant ✨');
    }
}
