<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\BoostPack;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Subscription;
use App\Support\Payments\PaydunyaGateway;
use App\Support\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminBillingController extends Controller
{
    /* ---------------- Plans ---------------- */

    public function plans()
    {
        $plans = Plan::orderBy('sort_order')->get();
        $boosts = BoostPack::orderBy('sort_order')->get();

        return view('admin.billing.plans', compact('plans', 'boosts'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:80'],
            'tagline'          => ['nullable', 'string', 'max:120'],
            'price'            => ['required', 'integer', 'min:0'],
            'compare_at_price' => ['nullable', 'integer', 'min:0'],
            'duration_days'    => ['nullable', 'integer', 'min:1'],
            'features'         => ['nullable', 'string'],
            'is_premium'       => ['nullable', 'boolean'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $features = collect(preg_split('/\r\n|\r|\n/', (string) ($data['features'] ?? '')))
            ->map(fn ($l) => trim($l))->filter()->values()->all();

        $plan->update([
            'name'             => $data['name'],
            'tagline'          => $data['tagline'] ?? null,
            'price'            => $data['price'],
            'compare_at_price' => $data['compare_at_price'] ?: null,
            'duration_days'    => $data['duration_days'] ?: null,
            'features'         => $features,
            'is_premium'       => $request->boolean('is_premium'),
            'is_active'        => $request->boolean('is_active'),
        ]);

        AdminLog::record($request->user()->id, 'plan_update', Plan::class, $plan->id, $plan->name);

        return back()->with('status', "Formule « {$plan->name} » mise à jour.");
    }

    public function updateBoost(Request $request, BoostPack $boostPack)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:80'],
            'price'         => ['required', 'integer', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $boostPack->update([
            'name'          => $data['name'],
            'price'         => $data['price'],
            'duration_days' => $data['duration_days'],
            'is_active'     => $request->boolean('is_active'),
        ]);

        AdminLog::record($request->user()->id, 'boost_update', BoostPack::class, $boostPack->id, $boostPack->name);

        return back()->with('status', "Boost « {$boostPack->name} » mis à jour.");
    }

    /* ---------------- Abonnements ---------------- */

    public function subscriptions(Request $request)
    {
        $tab = $request->input('tab', 'subscriptions');
        $status = $request->input('status');

        if ($tab === 'boosts') {
            $boosts = \App\Models\Boost::with(['user', 'pack'])
                ->latest()
                ->paginate(20)
                ->withQueryString();
        } else {
            $boosts = null;
        }

        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'active'        => Subscription::where('status', 'active')->count(),
            'revenue'       => Subscription::whereIn('status', ['active', 'expired'])->sum('amount'),
            'pending'       => Subscription::where('status', 'pending')->count(),
            'boosts_active' => \App\Models\Boost::where('ends_at', '>', now())->count(),
            'boosts_total'  => \App\Models\Boost::whereNotNull('starts_at')->count(),
            'boosts_rev'    => (int) \App\Models\Boost::whereNotNull('starts_at')->sum('amount'),
        ];

        return view('admin.billing.subscriptions', compact('subscriptions', 'boosts', 'tab', 'status', 'stats'));
    }

    /** Annule manuellement un abonnement actif. */
    public function cancelSubscription(Request $request, Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        AdminLog::record($request->user()->id, 'subscription_cancel', Subscription::class, $subscription->id);

        return back()->with('status', 'Abonnement annulé.');
    }

    /* ---------------- Paramètres de paiement ---------------- */

    public function payment()
    {
        $settings = [
            'provider'      => Setting::get('payment.provider', 'stub'),
            'mode'          => Setting::get('payment.paydunya_mode', 'test'),
            'master_key'    => Setting::get('payment.paydunya_master_key', ''),
            'private_key'   => Setting::get('payment.paydunya_private_key', ''),
            'token'         => Setting::get('payment.paydunya_token', ''),
        ];

        $configured = (new PaydunyaGateway())->isConfigured();
        $active = PaymentManager::gateway();

        return view('admin.billing.payment', compact('settings', 'configured', 'active'));
    }

    public function savePayment(Request $request)
    {
        $data = $request->validate([
            'provider'    => ['required', Rule::in(['stub', 'paydunya'])],
            'mode'        => ['required', Rule::in(['test', 'live'])],
            'master_key'  => ['nullable', 'string', 'max:255'],
            'private_key' => ['nullable', 'string', 'max:255'],
            'token'       => ['nullable', 'string', 'max:255'],
        ]);

        Setting::put('payment.provider', $data['provider'], 'string', 'payment');
        Setting::put('payment.paydunya_mode', $data['mode'], 'string', 'payment');
        Setting::put('payment.paydunya_master_key', $data['master_key'] ?? '', 'string', 'payment');
        Setting::put('payment.paydunya_private_key', $data['private_key'] ?? '', 'string', 'payment');
        Setting::put('payment.paydunya_token', $data['token'] ?? '', 'string', 'payment');

        AdminLog::record($request->user()->id, 'payment_settings', null, null, 'Paramètres de paiement mis à jour');

        return back()->with('status', 'Paramètres de paiement enregistrés.');
    }
}
