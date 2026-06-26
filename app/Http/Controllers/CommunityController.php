<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    public const THEMES = ['Tout', 'Confession', 'Conseil', 'Témoignage', 'Question'];

    /** Fil d'actualité : publications récentes paginées, filtrables par thème. */
    public function index(Request $request)
    {
        $theme = $request->input('theme');
        $tag   = trim((string) $request->input('tag'), " \t\n\r\0\x0B#");

        $query = Post::with(['author.profile', 'postReactions'])
            ->withCount('postComments')
            ->latest('published_at');

        if ($theme && $theme !== 'Tout' && in_array($theme, self::THEMES, true)) {
            $query->where('theme', $theme);
        }

        if ($tag !== '') {
            $query->where('body', 'like', '%#' . $tag . '%');
        }

        $posts = $query->paginate(8)->withQueryString();

        $online = \App\Models\User::query()->online()
            ->whereHas('profile')
            ->with('profile')
            ->latest('last_seen_at')
            ->take(8)->get();

        $uid = $request->user()?->id;
        $savedIds = $uid ? \DB::table('saved_posts')->where('user_id', $uid)->pluck('post_id')->all() : [];

        return view('communaute.index', [
            'posts'    => $posts,
            'online'   => $online,
            'themes'   => self::THEMES,
            'theme'    => $theme ?: 'Tout',
            'tag'      => $tag !== '' ? $tag : null,
            'savedIds' => $savedIds,
        ]);
    }

    /** Défilement infini : renvoie le HTML des publications de la page demandée. */
    public function loadFeed(Request $request)
    {
        $theme = $request->input('theme');
        $tag   = trim((string) $request->input('tag'), " \t\n\r\0\x0B#");

        $query = Post::with(['author.profile', 'postReactions'])
            ->withCount('postComments')
            ->latest('published_at');

        if ($theme && $theme !== 'Tout' && in_array($theme, self::THEMES, true)) {
            $query->where('theme', $theme);
        }
        if ($tag !== '') {
            $query->where('body', 'like', '%#' . $tag . '%');
        }

        $posts = $query->paginate(8);
        $uid = $request->user()?->id;
        $savedIds = $uid ? \DB::table('saved_posts')->where('user_id', $uid)->pluck('post_id')->all() : [];

        $html = '';
        foreach ($posts as $p) {
            $html .= view('partials.post-card', ['p' => $p, 'stagger' => 0, 'savedIds' => $savedIds])->render();
        }

        return response()->json([
            'html'    => $html,
            'hasMore' => $posts->hasMorePages(),
            'next'    => $posts->currentPage() + 1,
        ]);
    }

    /** Enregistrer / retirer une publication (« pense-y »). */
    public function toggleSave(Request $request, Post $post)
    {
        $result = $request->user()->savedPosts()->toggle($post->id);

        return response()->json(['saved' => count($result['attached']) > 0]);
    }

    /** Voter à un sondage (un vote par membre, modifiable). */
    public function vote(Request $request, Post $post)
    {
        abort_unless(is_array($post->poll) && count($post->poll) >= 2, 404);

        $data = $request->validate([
            'choice' => ['required', 'integer', 'min:0', 'max:' . (count($post->poll) - 1)],
        ]);

        \App\Models\PostVote::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => $request->user()->id],
            ['choice' => $data['choice']],
        );

        return response()->json($post->pollData($request->user()->id));
    }

    /** Liste des publications enregistrées par le membre. */
    public function saved(Request $request)
    {
        $posts = $request->user()->savedPosts()
            ->with(['author.profile', 'postReactions'])
            ->withCount('postComments')
            ->orderByPivot('id', 'desc')
            ->paginate(10);

        return view('espace.enregistrements', [
            'posts'    => $posts,
            'savedIds' => $posts->pluck('id')->all(),
        ]);
    }

    /** Publier une nouvelle publication. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'body'           => ['required', 'string', 'max:2000'],
            'theme'          => ['nullable', 'string', 'max:40'],
            'is_anonymous'   => ['nullable', 'boolean'],
            'image'          => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'poll_options'   => ['nullable', 'array', 'max:4'],
            'poll_options.*' => ['nullable', 'string', 'max:80'],
        ], [], ['body' => 'message', 'image' => 'image']);

        $poll = collect($request->input('poll_options', []))
            ->map(fn ($o) => trim((string) $o))->filter()->take(4)->values();
        $poll = $poll->count() >= 2 ? $poll->all() : null;

        $themes = [
            'Confession' => '🌙', 'Conseil' => '💡', 'Témoignage' => '💍', 'Question' => '❓',
        ];
        $theme = in_array($data['theme'] ?? null, array_keys($themes)) ? $data['theme'] : 'Confession';

        $user = $request->user();

        $image = $request->hasFile('image')
            ? \App\Support\ImageOptimizer::fromUpload($request->file('image'), 'post' . $user->id, 1200)
            : null;

        $post = Post::create([
            'user_id'         => $user->id,
            'author_name'     => $user->name,
            'is_anonymous'    => $request->boolean('is_anonymous'),
            'author_verified' => ($user->profile?->verification_level ?? 'Bronze') !== 'Bronze',
            'theme'           => $theme,
            'theme_emoji'     => $themes[$theme],
            'location'        => $user->profile?->region,
            'body'            => $data['body'],
            'poll'            => $poll,
            'image'           => $image,
            'hearts'          => 0,
            'replies'         => 0,
            'reactions'       => [],
            'comments'        => [],
            'published_at'    => now(),
        ]);

        $this->notifyMentions($data['body'], $user, route('communaute.show', $post));

        // Diffusion temps réel (si le serveur Reverb tourne ; sinon ignoré silencieusement)
        try {
            \App\Events\CommunityPostCreated::dispatch($post->id, $post->theme);
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Votre publication a été partagée avec la communauté. 🤲');
    }

    /** Nombre de nouvelles publications depuis un instant donné (JSON, pour le polling). */
    public function newPosts(Request $request)
    {
        $since = $request->input('since');
        $theme = $request->input('theme');
        $tag   = trim((string) $request->input('tag'), " \t\n\r\0\x0B#");

        $query = Post::query();

        if ($since) {
            $query->where('published_at', '>', \Carbon\Carbon::parse($since));
        }
        if ($theme && $theme !== 'Tout' && in_array($theme, self::THEMES, true)) {
            $query->where('theme', $theme);
        }
        if ($tag !== '') {
            $query->where('body', 'like', '%#' . $tag . '%');
        }

        return response()->json(['count' => $query->count()]);
    }

    /** Compteurs (réactions/commentaires) à jour pour une liste de publications (JSON, pour le polling). */
    public function counters(Request $request)
    {
        $ids = array_slice(array_map('intval', (array) $request->input('ids', [])), 0, 30);

        $posts = Post::with('postReactions')->withCount('postComments')->whereIn('id', $ids)->get();

        return response()->json($posts->map(function (Post $p) {
            $digest = $p->reactionDigest();

            return [
                'id'       => $p->id,
                'total'    => $digest['total'],
                'emojis'   => $digest['emojis'],
                'comments' => $p->post_comments_count,
            ];
        })->values());
    }

    /** Pose / change / retire une réaction (JSON). */
    public function react(Request $request, Post $post)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(PostReaction::TYPES))],
        ]);

        $me = $request->user();
        $existing = $post->postReactions()->where('user_id', $me->id)->first();

        if ($existing && $existing->type === $data['type']) {
            $existing->delete(); // re-clic sur la même réaction = retrait
            $mine = null;
        } else {
            $post->postReactions()->updateOrCreate(
                ['user_id' => $me->id],
                ['type' => $data['type']],
            );
            $mine = $data['type'];

            if ($post->user_id && $post->user_id !== $me->id) {
                \App\Models\AppNotification::record(
                    $post->user_id, $me->id, 'community',
                    $me->name . ' a réagi à votre publication.',
                    route('communaute.show', $post),
                );
            }
        }

        $post->load('postReactions');
        $digest = $post->reactionDigest();

        return response()->json([
            'mine'   => $mine,
            'total'  => $digest['total'],
            'emojis' => $digest['emojis'],
        ]);
    }

    /** Charge les commentaires racine (derniers d'abord) + leurs réponses — JSON. */
    public function comments(Request $request, Post $post)
    {
        $meId = $request->user()?->id;

        $comments = $post->postComments()
            ->whereNull('parent_id')
            ->with(['user.profile', 'replies.user.profile'])
            ->withCount('likes')
            ->latest()
            ->paginate(5);

        return response()->json([
            'items'    => $comments->map(fn ($c) => $this->commentPayload($c, $meId, true))->all(),
            'has_more' => $comments->hasMorePages(),
            'next'     => $comments->currentPage() + 1,
            'total'    => $post->postComments()->whereNull('parent_id')->count(),
        ]);
    }

    /** Ajoute un commentaire ou une réponse (JSON). */
    public function storeComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'body'      => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'integer'],
        ], [], ['body' => 'commentaire']);

        $me = $request->user();

        // Validation du parent (doit appartenir au même post)
        $parentId = null;
        if (! empty($data['parent_id'])) {
            $parent = $post->postComments()->whereKey($data['parent_id'])->first();
            $parentId = $parent?->parent_id ?: $parent?->id; // pas de sur-imbrication : on rattache au commentaire racine
        }

        $comment = $post->postComments()->create([
            'user_id'   => $me->id,
            'parent_id' => $parentId,
            'body'      => $data['body'],
        ]);
        $comment->load('user.profile');
        $comment->likes_count = 0;

        if ($post->user_id && $post->user_id !== $me->id) {
            \App\Models\AppNotification::record(
                $post->user_id, $me->id, 'community',
                $me->name . ($parentId ? ' a répondu à un commentaire sur votre publication.' : ' a commenté votre publication.'),
                route('communaute.show', $post),
            );
        }

        $this->notifyMentions($data['body'], $me, route('communaute.show', $post));

        return response()->json([
            'comment'   => $this->commentPayload($comment, $me->id, false),
            'parent_id' => $parentId,
            'total'     => $post->postComments()->whereNull('parent_id')->count(),
        ]);
    }

    /** Like / unlike d'un commentaire (JSON). */
    public function likeComment(Request $request, \App\Models\Comment $comment)
    {
        $me = $request->user();
        $existing = $comment->likes()->where('user_id', $me->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $comment->likes()->create(['user_id' => $me->id]);
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $comment->likes()->count()]);
    }

    /** Signale une publication (JSON). */
    public function report(Request $request, Post $post)
    {
        return $this->storeReport($request, $post);
    }

    /** Signale un commentaire (JSON). */
    public function reportComment(Request $request, Comment $comment)
    {
        return $this->storeReport($request, $comment);
    }

    /** Notifie les membres mentionnés via @nom dans un texte. */
    private function notifyMentions(string $text, \App\Models\User $author, string $url): void
    {
        foreach (\App\Support\TextEnricher::mentionedUsers($text) as $mentioned) {
            if ($mentioned->id === $author->id) {
                continue;
            }
            \App\Models\AppNotification::record(
                $mentioned->id, $author->id, 'mention',
                $author->name . ' vous a mentionné dans la communauté.',
                $url,
            );
        }
    }

    private function storeReport(Request $request, Post|Comment $reportable)
    {
        $data = $request->validate([
            'reason' => ['required', Rule::in(array_keys(Report::REASONS))],
        ]);

        $me = $request->user();

        $report = Report::firstOrNew([
            'reporter_id'     => $me->id,
            'reportable_id'   => $reportable->id,
            'reportable_type' => $reportable->getMorphClass(),
        ]);
        $report->reason = $data['reason'];
        $report->status = 'pending';
        $report->save();

        return response()->json(['ok' => true, 'message' => 'Merci, votre signalement a été transmis à la modération.']);
    }

    private function commentPayload(\App\Models\Comment $c, ?int $meId, bool $withReplies): array
    {
        $p = $c->user?->profile;
        $photo = $p && $p->photo ? pathinfo($p->photo, PATHINFO_FILENAME) : \App\Support\Avatar::photo($c->user?->name);

        $long = \Illuminate\Support\Str::length($c->body) > 220;

        $data = [
            'id'      => $c->id,
            'name'    => $c->user?->name ?? 'Membre',
            'photo'   => asset('img/' . $photo . '.webp'),
            'body'    => \App\Support\TextEnricher::render($c->body),
            'long'    => $long,
            'excerpt' => $long ? e(\Illuminate\Support\Str::limit($c->body, 220)) : null,
            'ago'     => $c->created_at->locale('fr')->diffForHumans(),
            'likes'   => $c->likes_count ?? $c->likes()->count(),
            'liked'   => $meId ? $c->likes()->where('user_id', $meId)->exists() : false,
        ];

        if ($withReplies) {
            $data['replies'] = $c->replies->map(fn ($r) => $this->commentPayload($r, $meId, false))->all();
        }

        return $data;
    }
}
