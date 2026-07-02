<?php

namespace App\Http\Controllers;

use App\Models\CoinPack;
use App\Models\CoinTransaction;
use App\Models\Gift;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminCoinsController extends Controller
{
    public function index()
    {
        return view('admin.coins', [
            'packs'                => CoinPack::orderBy('sort_order')->get(),
            'gifts'                => Gift::orderBy('sort_order')->get(),
            'spotlightCost'        => Setting::get('spotlight_cost', 50),
            'spotlightHours'       => Setting::get('spotlight_hours', 24),
            'referralSignupBonus'  => Setting::get('referral_signup_bonus', 30),
            'referralPremiumBonus' => Setting::get('referral_premium_bonus', 100),
            'totalPurchased'       => (int) CoinTransaction::where('type', 'purchase')->sum('coins'),
            'totalSpent'           => (int) abs(CoinTransaction::where('coins', '<', 0)->sum('coins')),
            'recentTx'             => CoinTransaction::with('user:id,name')->latest()->take(20)->get(),
        ]);
    }

    public function updatePack(Request $request, CoinPack $coinPack)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:60'],
            'coins'       => ['required', 'integer', 'min:1'],
            'bonus_coins' => ['required', 'integer', 'min:0'],
            'price'       => ['required', 'integer', 'min:1'],
            'is_popular'  => ['boolean'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['required', 'integer', 'min:0'],
        ]);

        $coinPack->update($data + [
            'is_popular' => $request->boolean('is_popular'),
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('status', "Pack « {$coinPack->name} » mis à jour.");
    }

    public function updateGift(Request $request, Gift $gift)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:40'],
            'emoji'      => ['required', 'string', 'max:10'],
            'coins_cost' => ['required', 'integer', 'min:1'],
            'category'   => ['required', 'string', 'max:30'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active'  => ['boolean'],
        ]);

        $gift->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', "Cadeau « {$gift->name} » mis à jour.");
    }

    public function updateCost(Request $request)
    {
        $request->validate([
            'spotlight_cost'  => ['required', 'integer', 'min:1'],
            'spotlight_hours' => ['required', 'integer', 'min:1', 'max:168'],
        ]);

        $request->validate([
            'referral_signup_bonus'  => ['required', 'integer', 'min:0'],
            'referral_premium_bonus' => ['required', 'integer', 'min:0'],
        ]);

        Setting::put('spotlight_cost', (int) $request->input('spotlight_cost'), 'int', 'coins');
        Setting::put('spotlight_hours', (int) $request->input('spotlight_hours'), 'int', 'coins');
        Setting::put('referral_signup_bonus', (int) $request->input('referral_signup_bonus'), 'int', 'coins');
        Setting::put('referral_premium_bonus', (int) $request->input('referral_premium_bonus'), 'int', 'coins');

        return back()->with('status', 'Coûts du Spotlight et parrainage mis à jour.');
    }
}
