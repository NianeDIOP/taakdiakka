<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\FriendRequest;
use App\Models\User;
use App\Support\FeatureGate;

class FriendController extends Controller
{
    /** Envoie une demande d'ami (ou accepte une demande inverse en attente). */
    public function request(User $user)
    {
        $me = auth()->user();
        abort_if($user->id === $me->id, 403);

        // Une demande inverse en attente existe → on devient amis directement
        $reverse = FriendRequest::where('sender_id', $user->id)
            ->where('receiver_id', $me->id)
            ->where('status', 'pending')
            ->first();
        if ($reverse) {
            $reverse->update(['status' => 'accepted']);
            AppNotification::record($user->id, $me->id, 'friend_accept', "{$me->name} a accepté votre demande d'ami.", route('members.show', $me));
            \App\Support\Notifier::email($user, "{$me->name} a accepté votre demande d'ami 🤝", 'Vous êtes désormais amis',
                ["{$me->name} a accepté votre demande d'ami sur " . \App\Models\Setting::siteName() . '. Vous pouvez maintenant échanger librement.'],
                'Voir le profil', route('members.show', $me));

            return back()->with('status', "Vous êtes désormais amis avec {$user->name} ! 🤝");
        }

        $status = $me->friendStatusWith($user);
        if (in_array($status, ['pending_sent', 'friends'])) {
            return back()->with('status', 'Demande déjà en cours.');
        }

        // Abonnement requis pour envoyer une demande (selon la configuration admin)
        if (! FeatureGate::canSendFriendRequest($me)) {
            return redirect()->route('tarifs')
                ->with('status', "L'envoi de demandes d'ami est réservé aux membres abonnés. Découvrez nos formules ✨");
        }

        FriendRequest::firstOrCreate(
            ['sender_id' => $me->id, 'receiver_id' => $user->id],
            ['status' => 'pending'],
        );
        AppNotification::record($user->id, $me->id, 'friend_request', "{$me->name} vous a envoyé une demande d'ami.", route('friends.index'));
        \App\Support\Notifier::email($user, "{$me->name} souhaite vous ajouter 🤝", "Nouvelle demande d'ami",
            ["{$me->name} vous a envoyé une demande d'ami sur " . \App\Models\Setting::siteName() . '.', 'Consultez son profil et répondez à la demande depuis votre espace.'],
            'Voir la demande', route('friends.index'));

        return back()->with('status', "Demande d'ami envoyée à {$user->name}.");
    }

    /** Accepte une demande reçue. */
    public function accept(FriendRequest $friendRequest)
    {
        abort_unless($friendRequest->receiver_id === auth()->id(), 403);

        $friendRequest->update(['status' => 'accepted']);
        AppNotification::record(
            $friendRequest->sender_id,
            auth()->id(),
            'friend_accept',
            auth()->user()->name . ' a accepté votre demande d\'ami.',
            route('members.show', auth()->id()),
        );

        return back()->with('status', "Vous êtes désormais amis avec {$friendRequest->sender->name} ! 🤝");
    }

    /** Refuse une demande reçue. */
    public function decline(FriendRequest $friendRequest)
    {
        abort_unless($friendRequest->receiver_id === auth()->id(), 403);

        $friendRequest->delete();

        return back()->with('status', 'Demande refusée.');
    }

    /** Annule une demande envoyée (ou retire un ami). */
    public function cancel(FriendRequest $friendRequest)
    {
        abort_unless(
            $friendRequest->sender_id === auth()->id() || $friendRequest->receiver_id === auth()->id(),
            403,
        );

        $friendRequest->delete();

        return back()->with('status', 'Demande annulée.');
    }

    /** Hub « Demandes » : reçues, envoyées, messages. */
    public function index()
    {
        $me = auth()->user();

        $received = $me->friendRequestsReceived()->where('status', 'pending')
            ->with('sender.profile')->latest()->get();
        $sent = $me->friendRequestsSent()->where('status', 'pending')
            ->with('receiver.profile')->latest()->get();
        $friends = $me->friends()->with('profile')->get();
        $conversations = $me->conversations()
            ->with(['users', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('espace.demandes-hub', compact('received', 'sent', 'friends', 'conversations'));
    }
}
