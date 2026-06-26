<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Conversation;
use App\Models\Demande;
use App\Support\FeatureGate;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /** Liste des conversations. */
    public function index()
    {
        $this->markMessageNotificationsRead();

        return view('espace.messages', [
            'conversations' => $this->conversationsList(),
            'active'        => null,
            'messages'      => collect(),
        ]);
    }

    /** Affiche une conversation. */
    public function show(Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);
        $this->markMessageNotificationsRead();

        // Accusés de lecture : marque les messages reçus comme lus
        $conversation->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('espace.messages', [
            'conversations' => $this->conversationsList(),
            'active'        => $conversation,
            'messages'      => $conversation->messages()->with('user')->oldest()->get(),
        ]);
    }

    /** Envoi d'un message. */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorizeParticipant($conversation);

        // Limite de messages gratuits par contact (selon la configuration admin)
        $limit = FeatureGate::messagesPerContact(auth()->user());
        if ($limit !== PHP_INT_MAX) {
            $sent = $conversation->messages()->where('user_id', auth()->id())->count();
            if ($sent >= $limit) {
                return redirect()->route('tarifs')->with('status',
                    "Vous avez atteint la limite de {$limit} messages gratuits avec ce contact. Passez à l'abonnement pour discuter sans limite ✨");
            }
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [], ['body' => 'message']);

        $conversation->messages()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        // Notifie l'autre participant
        $other = $conversation->users->firstWhere('id', '!=', auth()->id());
        if ($other) {
            AppNotification::record(
                $other->id,
                auth()->id(),
                'message',
                auth()->user()->name . ' vous a envoyé un message.',
                route('messages.show', $conversation),
            );

            // E-mail seulement au 1er message de la conversation (évite le spam sur les réponses)
            if ($conversation->messages()->count() === 1) {
                \App\Support\Notifier::email($other, auth()->user()->name . ' vous a écrit 💬', 'Nouveau message',
                    [auth()->user()->name . ' vous a envoyé un message sur ' . \App\Models\Setting::siteName() . '.', 'Connectez-vous pour lire et répondre.'],
                    'Lire le message', route('messages.show', $conversation));
            }
        }

        return redirect()->route('messages.show', $conversation);
    }

    /** « Contacter » : ouvre (ou crée) la conversation avec l'auteur d'une demande. */
    public function contact(Demande $demande)
    {
        if (! $demande->user_id || $demande->user_id === auth()->id()) {
            return redirect()->route('messages')
                ->with('status', 'Conversation indisponible pour ce profil.');
        }

        $conversation = Conversation::findOrCreateBetween(auth()->id(), $demande->user_id);

        return redirect()->route('messages.show', $conversation);
    }

    private function conversationsList()
    {
        return auth()->user()->conversations()
            ->with(['users.profile', 'users.demandes:id,user_id,status', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->get();
    }

    private function authorizeParticipant(Conversation $conversation): void
    {
        abort_unless($conversation->users->contains('id', auth()->id()), 403);
    }

    /** Marque les notifications de type message comme lues (vide le badge ✉️). */
    private function markMessageNotificationsRead(): void
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->where('type', 'message')
            ->update(['read_at' => now()]);
    }
}
