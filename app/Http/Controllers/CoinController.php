<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\CoinPack;
use App\Models\CoinTransaction;
use App\Models\Gift;
use App\Models\SentGift;
use App\Models\User;
use App\Support\Payments\PaymentManager;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function shop()
    {
        return view('coins.shop', [
            'packs' => CoinPack::active()->get(),
            'balance' => auth()->user()->coins_balance,
        ]);
    }

    public function checkout(Request $request, CoinPack $coinPack)
    {
        abort_unless($coinPack->is_active, 404);

        $user = $request->user();

        $tx = CoinTransaction::create([
            'user_id'        => $user->id,
            'type'           => 'purchase_pending',
            'coins'          => $coinPack->total_coins,
            'balance_after'  => $user->coins_balance,
            'description'    => "Achat {$coinPack->name}",
            'reference_type' => CoinPack::class,
            'reference_id'   => $coinPack->id,
        ]);

        $result = PaymentManager::gateway()->initiate(
            ['amount' => $coinPack->price, 'label' => 'Pièces d\'or — ' . $coinPack->name, 'email' => $user->email],
            route('coins.callback', ['transaction' => $tx->id]),
            route('coins.shop'),
        );

        if (! $result->ok) {
            $tx->delete();
            return redirect()->route('coins.shop')->with('status', 'Paiement indisponible : ' . $result->error);
        }

        $tx->update(['description' => "Achat {$coinPack->name} — ref:{$result->reference}"]);

        return redirect()->away($result->redirectUrl);
    }

    public function callback(Request $request, CoinTransaction $transaction)
    {
        abort_unless($transaction->user_id === auth()->id(), 403);

        if ($transaction->type === 'purchase') {
            return redirect()->route('coins.shop')->with('status', 'Pièces déjà créditées !');
        }

        $confirmed = PaymentManager::gateway()->confirm($transaction->description);

        if (! $confirmed) {
            $transaction->delete();
            return redirect()->route('coins.shop')->with('status', 'Le paiement n\'a pas pu être confirmé.');
        }

        $user = $request->user();
        $newBalance = $user->coins_balance + $transaction->coins;
        $user->update(['coins_balance' => $newBalance]);
        $transaction->update(['type' => 'purchase', 'balance_after' => $newBalance]);

        return redirect()->route('coins.shop')->with('status', "🪙 {$transaction->coins} pièces d'or créditées ! Solde : {$newBalance}");
    }

    public function sendGift(Request $request, User $user)
    {
        $me = $request->user();
        abort_if($user->id === $me->id, 403);

        $data = $request->validate([
            'gift_id' => ['required', 'integer'],
            'message' => ['nullable', 'string', 'max:120'],
        ]);

        $gift = Gift::findOrFail($data['gift_id']);
        abort_unless($gift->is_active, 404);

        if ($me->coins_balance < $gift->coins_cost) {
            return back()->with('status', "Solde insuffisant ({$me->coins_balance} pièces). Rechargez votre compte.");
        }

        $newBalance = $me->coins_balance - $gift->coins_cost;
        $me->update(['coins_balance' => $newBalance]);

        CoinTransaction::create([
            'user_id'        => $me->id,
            'type'           => 'spend_gift',
            'coins'          => -$gift->coins_cost,
            'balance_after'  => $newBalance,
            'description'    => "Cadeau {$gift->name} → {$user->name}",
            'reference_type' => Gift::class,
            'reference_id'   => $gift->id,
        ]);

        SentGift::create([
            'sender_id'   => $me->id,
            'receiver_id' => $user->id,
            'gift_id'     => $gift->id,
            'message'     => $data['message'],
        ]);

        AppNotification::record(
            $user->id, $me->id, 'community',
            "{$me->name} vous a envoyé un cadeau {$gift->emoji} {$gift->name} !",
            route('members.show', $me),
        );

        return back()->with('status', "Cadeau {$gift->emoji} envoyé à {$user->name} !");
    }

    public function gifts()
    {
        return response()->json(Gift::active()->get(['id', 'name', 'emoji', 'coins_cost', 'category']));
    }
}
