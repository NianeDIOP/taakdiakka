<?php

namespace Database\Seeders;

use App\Models\Boost;
use App\Models\BoostPack;
use App\Models\FriendRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Peuple les interactions entre membres de démonstration : amitiés, intérêts,
 * matchs, suivis, favoris, visites, abonnements et boosts.
 * Idempotent : repart d'une base propre pour les membres de démo à chaque exécution.
 */
class EngagementSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::where('email', 'like', '%@membre.taakdiakka.test')
            ->with(['profile', 'demandes'])->get();

        if ($members->count() < 6) {
            return; // membres non seedés
        }

        $ids = $members->pluck('id')->all();
        $women = $members->filter(fn ($u) => $u->profile?->gender === 'Femme')->values();
        $men   = $members->filter(fn ($u) => $u->profile?->gender === 'Homme')->values();

        // --- Repart propre (seulement pour les membres de démo) ---
        DB::table('reports')->whereIn('reporter_id', $ids)->delete();
        DB::table('friend_requests')->whereIn('sender_id', $ids)->orWhereIn('receiver_id', $ids)->delete();
        DB::table('interests')->whereIn('user_id', $ids)->orWhereIn('target_user_id', $ids)->delete();
        DB::table('follows')->whereIn('follower_id', $ids)->orWhereIn('followed_id', $ids)->delete();
        DB::table('favorites')->whereIn('user_id', $ids)->delete();
        DB::table('profile_views')->whereIn('viewer_id', $ids)->orWhereIn('viewed_id', $ids)->delete();
        DB::table('subscriptions')->whereIn('user_id', $ids)->delete();
        DB::table('boosts')->whereIn('user_id', $ids)->delete();
        DB::table('comment_likes')->delete();
        DB::table('comments')->delete();
        DB::table('messages')->delete();
        DB::table('conversation_user')->delete();
        DB::table('conversations')->delete();

        $now = Carbon::now();

        /* ---------------- Amitiés (acceptées) + demandes en attente ---------------- */
        $friendPairs = [[0, 0], [1, 1], [0, 2], [3, 3], [2, 4]]; // [hommeIndex, femmeIndex]
        foreach ($friendPairs as $j => [$mi, $wi]) {
            if (! isset($men[$mi], $women[$wi])) {
                continue;
            }
            FriendRequest::create([
                'sender_id'   => $men[$mi]->id,
                'receiver_id' => $women[$wi]->id,
                'status'      => 'accepted',
                'created_at'  => $now->copy()->subDays(10 - $j),
            ]);
        }
        // En attente
        $pending = [[4, 0], [5, 1], [2, 3]];
        foreach ($pending as [$mi, $wi]) {
            if (isset($men[$mi], $women[$wi])) {
                FriendRequest::create([
                    'sender_id' => $men[$mi]->id, 'receiver_id' => $women[$wi]->id, 'status' => 'pending',
                ]);
            }
        }

        /* ---------------- Intérêts + matchs (intérêt réciproque) ---------------- */
        $interests = [];
        // Matchs réciproques
        $matches = [[0, 0], [1, 1]];
        foreach ($matches as [$mi, $wi]) {
            if (isset($men[$mi], $women[$wi])) {
                $interests[] = [$men[$mi]->id, $women[$wi]->id];
                $interests[] = [$women[$wi]->id, $men[$mi]->id];
            }
        }
        // Intérêts simples (non réciproques)
        $oneWay = [[2, 4], [3, 2], [5, 0], [6, 1]];
        foreach ($oneWay as [$mi, $wi]) {
            if (isset($men[$mi], $women[$wi])) {
                $interests[] = [$men[$mi]->id, $women[$wi]->id];
            }
        }
        foreach ($interests as [$from, $to]) {
            DB::table('interests')->insert([
                'user_id' => $from, 'target_user_id' => $to, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        /* ---------------- Suivis ---------------- */
        // Les 6 premiers membres suivent la 1re femme (profil « populaire »)
        if (isset($women[0])) {
            foreach ($members->take(7) as $u) {
                if ($u->id !== $women[0]->id) {
                    DB::table('follows')->insert([
                        'follower_id' => $u->id, 'followed_id' => $women[0]->id,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
        }

        /* ---------------- Favoris (par demande) ---------------- */
        foreach ($men->take(5) as $j => $m) {
            $target = $women[$j % max(1, $women->count())] ?? null;
            $demandeId = $target?->demandes->first()?->id;
            if ($demandeId) {
                DB::table('favorites')->insert([
                    'user_id' => $m->id, 'demande_id' => $demandeId,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        /* ---------------- Visites de profil (visiteurs) ---------------- */
        // Beaucoup de membres ont visité les 1res femmes et le 1er homme
        $viewed = collect([$women[0] ?? null, $women[1] ?? null, $men[0] ?? null])->filter();
        foreach ($viewed as $target) {
            foreach ($members as $viewer) {
                if ($viewer->id === $target->id || (crc32($viewer->id . '-' . $target->id) % 3) !== 0) {
                    continue;
                }
                DB::table('profile_views')->insert([
                    'viewer_id' => $viewer->id, 'viewed_id' => $target->id,
                    'created_at' => $now->copy()->subHours($viewer->id % 48),
                    'updated_at' => $now->copy()->subHours($viewer->id % 48),
                ]);
            }
        }

        /* ---------------- Commentaires + réponses + j'aime ---------------- */
        $commentBodies = [
            'Qu\'Allah facilite, très beau message 🤲',
            'MachaAllah, je partage totalement ton point de vue.',
            'Courage, ta moitié arrive inchaAllah 🌙',
            'Merci pour ce témoignage, ça fait du bien à lire.',
            'Amine 🙏 puisse Allah unir les cœurs sincères.',
            'Très juste — le respect et la foi avant tout.',
            'Belle énergie, continue ainsi ✨',
            'Je me reconnais dans tes mots, baraka Allahou fik.',
            'Que de sagesse, merci du partage 🌹',
            'On a besoin de plus de messages comme celui-ci.',
        ];
        $replyBodies = ['Amine 🤲', 'Tout à fait d\'accord 🙏', 'Merci à toi 🌹', 'Bien dit !', 'Qu\'il en soit ainsi inchaAllah.'];

        foreach (\App\Models\Post::all() as $post) {
            $count = 2 + ($post->id % 4); // 2 à 5 commentaires racine
            for ($i = 0; $i < $count; $i++) {
                $author = $members[($post->id * 5 + $i * 7) % $members->count()];
                $comment = $post->postComments()->create([
                    'user_id' => $author->id,
                    'parent_id' => null,
                    'body' => $commentBodies[($post->id + $i) % count($commentBodies)],
                ]);
                $comment->created_at = $now->copy()->subHours(($post->id + $i) % 70);
                $comment->save();

                // J'aime sur le commentaire
                foreach ($members->shuffle()->take(($i + $post->id) % 5) as $liker) {
                    DB::table('comment_likes')->insertOrIgnore([
                        'comment_id' => $comment->id, 'user_id' => $liker->id,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }

                // Une réponse un commentaire sur deux
                if ($i % 2 === 0) {
                    $replier = $members[($post->id + $i + 3) % $members->count()];
                    $post->postComments()->create([
                        'user_id' => $replier->id,
                        'parent_id' => $comment->id,
                        'body' => $replyBodies[($post->id + $i) % count($replyBodies)],
                    ]);
                }
            }
        }

        /* ---------------- Conversations + messages (entre amis) ---------------- */
        $convoStarters = [
            'Assalamou alaykoum, ravi(e) de faire ta connaissance 🙂',
            'Bonjour ! Merci d\'avoir accepté ma demande.',
            'Salam, comment se passe ta journée inchaAllah ?',
        ];
        $convoReplies = [
            'Wa alaykoum salam, enchanté(e) également 🤲',
            'Bonjour, avec plaisir ! Parle-moi un peu de toi.',
            'Très bien al hamdoulillah, et toi ?',
            'Merci pour ton message, j\'apprécie ton sérieux.',
        ];
        foreach ($friendPairs as $k => [$mi, $wi]) {
            if (! isset($men[$mi], $women[$wi])) {
                continue;
            }
            $a = $men[$mi]->id;
            $b = $women[$wi]->id;
            $conv = \App\Models\Conversation::findOrCreateBetween($a, $b);
            $line = [
                [$a, $convoStarters[$k % count($convoStarters)], true],
                [$b, $convoReplies[$k % count($convoReplies)], true],
                [$a, $convoReplies[($k + 1) % count($convoReplies)], $k % 2 === 0],
            ];
            $t = $now->copy()->subHours(($k + 1) * 5);
            foreach ($line as $idx => [$uid, $body, $read]) {
                $conv->messages()->create([
                    'user_id' => $uid,
                    'body' => $body,
                    'read_at' => $read ? $t->copy()->addMinutes(2) : null,
                    'created_at' => $t->copy()->addMinutes($idx * 3),
                    'updated_at' => $t->copy()->addMinutes($idx * 3),
                ]);
            }
            $conv->update(['last_message_at' => $t->copy()->addMinutes(10)]);
        }

        /* ---------------- Abonnements premium actifs ---------------- */
        $mensuel = Plan::where('slug', 'mensuel')->first();
        $annuel  = Plan::where('slug', 'annuel')->first();
        $subscribers = [
            [$women[0] ?? null, $mensuel, 4],
            [$men[0] ?? null, $annuel, 9],
            [$women[4] ?? null, $mensuel, 1],
            [$men[2] ?? null, $mensuel, 20],
        ];
        foreach ($subscribers as [$u, $plan, $daysAgo]) {
            if ($u && $plan) {
                Subscription::create([
                    'user_id'          => $u->id,
                    'plan_id'          => $plan->id,
                    'status'           => 'active',
                    'starts_at'        => $now->copy()->subDays($daysAgo),
                    'ends_at'          => $now->copy()->addDays(($plan->duration_days ?? 30) - $daysAgo),
                    'amount'           => $plan->price,
                    'payment_provider' => 'stub',
                ]);
            }
        }

        /* ---------------- Signalements en attente (pour la modération) ---------------- */
        $posts = \App\Models\Post::latest('id')->take(2)->get();
        $reasons = ['spam', 'inapproprie'];
        foreach ($posts as $idx => $post) {
            $reporter = $members[($idx + 3) % $members->count()];
            \App\Models\Report::firstOrCreate(
                ['reporter_id' => $reporter->id, 'reportable_id' => $post->id, 'reportable_type' => $post->getMorphClass()],
                ['reason' => $reasons[$idx] ?? 'autre', 'status' => 'pending'],
            );
        }

        /* ---------------- Boosts de visibilité actifs ---------------- */
        $pack = BoostPack::where('slug', 'boost-7j')->first() ?? BoostPack::first();
        foreach ([$women[0] ?? null, $men[1] ?? null] as $u) {
            if ($u && $pack) {
                Boost::create([
                    'user_id'       => $u->id,
                    'boost_pack_id' => $pack->id,
                    'starts_at'     => $now->copy()->subDay(),
                    'ends_at'       => $now->copy()->addDays($pack->duration_days - 1),
                    'amount'        => $pack->price,
                ]);
            }
        }
    }
}
