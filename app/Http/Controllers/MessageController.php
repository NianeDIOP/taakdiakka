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

        $me = auth()->user();
        $other = $conversation->users->firstWhere('id', '!=', $me->id);

        // Règle : abonné Premium ET amis acceptés pour pouvoir écrire
        if ($other && ! FeatureGate::canSendMessage($me, $other)) {
            if (FeatureGate::messageBlockReason($me, $other) === 'premium') {
                return redirect()->route('tarifs')->with('status',
                    "La messagerie est réservée aux membres abonnés. Découvrez nos formules pour discuter ✨");
            }

            return back()->with('status',
                "Vous devez d'abord être amis acceptés avec ce membre pour lui écrire. 🤝");
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], [], ['body' => 'message']);

        // Sécurité : partage de numéro de téléphone interdit tant que ≤ 10 messages échangés.
        // On détecte aussi les numéros envoyés « par petits lots » sur plusieurs messages.
        if ($conversation->messages()->count() <= 10) {
            $senderRecent = $conversation->messages()
                ->where('user_id', $me->id)
                ->latest()->take(8)->pluck('body')->all();

            if (\App\Support\PhoneGuard::containsPhone($data['body'])
                || \App\Support\PhoneGuard::assembledPhone($senderRecent, $data['body'])
                || \App\Support\ContactGuard::containsContact($data['body'])) {
                return back()->withInput()->with('status',
                    "🔒 Pour votre sécurité, le partage d'un numéro, d'une adresse e-mail ou d'un réseau (WhatsApp, Instagram…) — même découpé en plusieurs morceaux — n'est autorisé qu'après plus de 10 messages échangés. Prenez d'abord le temps de faire connaissance. 🤲");
            }
        }

        $conversation->messages()->create([
            'user_id' => $me->id,
            'body'    => $data['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        // Notifie l'autre participant (déjà calculé plus haut)
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

        // Règle : abonné Premium ET amis acceptés
        $me = auth()->user();
        $target = $demande->user;
        if ($target && ! FeatureGate::canSendMessage($me, $target)) {
            if (FeatureGate::messageBlockReason($me, $target) === 'premium') {
                return redirect()->route('tarifs')->with('status',
                    "La messagerie est réservée aux membres abonnés. Découvrez nos formules ✨");
            }

            return redirect()->route('members.show', $target)->with('status',
                "Vous devez d'abord être amis acceptés pour écrire à {$target->name}. 🤝");
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
