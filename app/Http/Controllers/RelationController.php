<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Conversation;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RelationController extends Controller
{
    /** Bloque un membre : coupe messages et demandes, masque le profil de part et d'autre. */
    public function block(User $user)
    {
        $me = auth()->user();
        abort_if($user->id === $me->id, 403);

        $me->blockedUsers()->syncWithoutDetaching([$user->id]);

        return back()->with('status', "{$user->name} a été bloqué(e). Ce membre ne pourra plus vous contacter et n'apparaîtra plus dans vos listes.");
    }

    /** Débloque un membre. */
    public function unblock(User $user)
    {
        auth()->user()->blockedUsers()->detach($user->id);

        return back()->with('status', "{$user->name} a été débloqué(e).");
    }

    /** Signale un membre à la modération. */
    public function report(Request $request, User $user)
    {
        $me = auth()->user();
        abort_if($user->id === $me->id, 403);

        $data = $request->validate([
            'reason' => ['required', Rule::in(array_keys(Report::REASONS))],
        ]);

        $report = Report::firstOrNew([
            'reporter_id'     => $me->id,
            'reportable_id'   => $user->id,
            'reportable_type' => $user->getMorphClass(),
        ]);
        $report->reason = $data['reason'];
        $report->status = 'pending';
        $report->save();

        return back()->with('status', "Merci, votre signalement concernant {$user->name} a été transmis à la modération. 🛡️");
    }

    /** Marque / retire l'intérêt envers un membre. */
    public function toggleInterest(User $user)
    {
        $me = auth()->user();
        abort_if($user->id === $me->id, 403);

        $me->interestsGiven()->toggle($user->id);

        if ($me->isMatchedWith($user)) {
            AppNotification::record($user->id, $me->id, 'interest', "C'est un match avec {$me->name} ! 💞", route('matchs'));

            return back()->with('status', "C'est un match avec {$user->name} ! Vous pouvez échanger librement. 💞");
        }

        if ($me->isInterestedIn($user)) {
            AppNotification::record($user->id, $me->id, 'interest', "{$me->name} s'intéresse à votre profil.", route('members.show', $me));

            return back()->with('status', "Votre intérêt a été envoyé à {$user->name}.");
        }

        return back()->with('status', 'Intérêt retiré.');
    }

    /** Page « Matchs & intérêts ». */
    public function matchs()
    {
        $me = auth()->user();
        $matchs = $me->matchedUsers()->with('profile')->get();
        $matchIds = $matchs->pluck('id');
        $received = $me->interestsReceived()->whereNotIn('users.id', $matchIds)->with('profile')->latest('interests.created_at')->get();

        return view('espace.matchs', compact('matchs', 'received'));
    }

    /** Ouvre (ou crée) une conversation avec un membre. */
    public function startConversation(User $user)
    {
        abort_if($user->id === auth()->id(), 403);

        // Règle : abonné Premium ET amis acceptés pour ouvrir une conversation
        $me = auth()->user();
        if (! \App\Support\FeatureGate::canSendMessage($me, $user)) {
            if (\App\Support\FeatureGate::messageBlockReason($me, $user) === 'premium') {
                return redirect()->route('tarifs')->with('status',
                    "La messagerie est réservée aux membres abonnés. Découvrez nos formules pour discuter ✨");
            }

            return redirect()->route('members.show', $user)->with('status',
                "Vous devez d'abord devenir amis (demande acceptée) pour écrire à {$user->name}. 🤝");
        }

        $conversation = Conversation::findOrCreateBetween(auth()->id(), $user->id);

        return redirect()->route('messages.show', $conversation);
    }

    /** Suit / ne suit plus un membre. */
    public function toggleFollow(User $user)
    {
        $me = auth()->user();
        abort_if($user->id === $me->id, 403);

        $me->following()->toggle($user->id);

        return back()->with('status', $me->isFollowing($user)
            ? "Vous suivez désormais {$user->name}."
            : "Vous ne suivez plus {$user->name}.");
    }

    /** Page « Visiteurs » : qui a vu mon profil + qui me suit. */
    public function visitors()
    {
        $me = auth()->user();
        $visitors  = $me->profileVisitors()->with('profile')->get();
        $followers = $me->followers()->with('profile')->get();
        $followingIds = $me->following()->pluck('users.id');

        return view('espace.visiteurs', compact('visitors', 'followers', 'followingIds'));
    }
}
