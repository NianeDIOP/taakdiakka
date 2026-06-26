<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'author_name', 'is_anonymous', 'author_verified', 'theme', 'theme_emoji',
        'location', 'body', 'poll', 'image', 'hearts', 'replies', 'reactions', 'comments', 'published_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'author_verified' => 'boolean',
        'reactions' => 'array',
        'comments' => 'array',
        'poll' => 'array',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes()
    {
        return $this->hasMany(PostVote::class);
    }

    /** Résultats du sondage : options + nombre de votes + %, et le vote du membre courant. */
    public function pollData(?int $userId): ?array
    {
        if (empty($this->poll) || ! is_array($this->poll)) {
            return null;
        }

        $tally = $this->votes()->selectRaw('choice, COUNT(*) as c')->groupBy('choice')->pluck('c', 'choice');
        $total = (int) $tally->sum();
        $myVote = $userId ? optional($this->votes()->where('user_id', $userId)->first())->choice : null;

        $options = [];
        foreach ($this->poll as $i => $label) {
            $count = (int) ($tally[$i] ?? 0);
            $options[] = [
                'i'     => $i,
                'label' => $label,
                'count' => $count,
                'pct'   => $total > 0 ? (int) round($count / $total * 100) : 0,
            ];
        }

        return ['options' => $options, 'total' => $total, 'myVote' => $myVote];
    }

    public function postReactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public function postComments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Réaction de l'utilisateur courant (type ou null). */
    public function myReaction(?int $userId): ?string
    {
        if (! $userId) {
            return null;
        }

        return $this->postReactions->where('user_id', $userId)->first()?->type;
    }

    /** Top 3 emojis distincts + total des réactions. */
    public function reactionDigest(): array
    {
        $byType = $this->postReactions->groupBy('type');
        $emojis = $byType->keys()
            ->sortByDesc(fn ($t) => $byType[$t]->count())
            ->map(fn ($t) => \App\Models\PostReaction::TYPES[$t][0] ?? '👍')
            ->take(3)->values()->all();

        return ['emojis' => $emojis, 'total' => $this->postReactions->count()];
    }

    /** Nom affiché : « Anonyme » si anonyme, sinon le membre auteur ou le nom legacy. */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonyme';
        }

        return $this->author?->name ?: ($this->author_name ?: 'Membre');
    }

    /** Nom de fichier de la photo d'avatar (ou null pour le monogramme). */
    public function getAuthorPhotoAttribute(): ?string
    {
        if ($this->is_anonymous) {
            return null;
        }
        $p = $this->author?->profile;
        if ($p && $p->photo) {
            return pathinfo($p->photo, PATHINFO_FILENAME);
        }
        $name = $this->author?->name ?: $this->author_name;

        return $name ? \App\Support\Avatar::photo($name) : null;
    }

    /** Initiale(s) pour l'avatar monogramme. */
    public function getInitialAttribute(): string
    {
        if ($this->is_anonymous || ! $this->author_name) {
            return '?';
        }
        $parts = preg_split('/\s+/', trim($this->author_name));
        $i = mb_substr($parts[0], 0, 1);
        if (count($parts) > 1) {
            $i .= mb_substr(end($parts), 0, 1);
        }
        return mb_strtoupper($i);
    }

    /** Nom de fichier de l'image jointe (sans extension), ou null. */
    public function getImageBaseAttribute(): ?string
    {
        return $this->image ? pathinfo($this->image, PATHINFO_FILENAME) : null;
    }

    /** Date / heure relative : « 7 juin, 14h32 » façon réseau social → on garde la date courte FR. */
    public function getPostedAttribute(): string
    {
        return optional($this->published_at)->locale('fr')->isoFormat('D MMM, HH[h]mm') ?? '';
    }
}
