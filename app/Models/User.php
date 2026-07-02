<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen_at',
        'last_device',
        'last_browser',
        'last_ip',
        'is_admin',
        'role',
        'status',
        'status_reason',
        'suspended_until',
        'email_opt_in',
        'coins_balance',
        'referral_code',
        'referred_by',
        'referral_bonus_paid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'last_seen_at'         => 'datetime',
            'password'             => 'hashed',
            'is_admin'             => 'boolean',
            'email_opt_in'         => 'boolean',
            'suspended_until'      => 'datetime',
            'referral_bonus_paid'  => 'boolean',
        ];
    }

    /** Membres (hors admin) actifs dans les 5 dernières minutes. */
    public function scopeOnline($query)
    {
        return $query->whereNull('role')
            ->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    /* ---- Parrainage ---- */

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    public function photos()
    {
        return $this->hasMany(ProfilePhoto::class)->latest('id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Demande::class, 'favorites')->withTimestamps();
    }

    public function savedPosts()
    {
        return $this->belongsToMany(Post::class, 'saved_posts')->withTimestamps();
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)->withTimestamps();
    }

    /* ---- Intérêt / Match ---- */
    public function interestsGiven()
    {
        return $this->belongsToMany(User::class, 'interests', 'user_id', 'target_user_id')->withTimestamps();
    }

    public function interestsReceived()
    {
        return $this->belongsToMany(User::class, 'interests', 'target_user_id', 'user_id')->withTimestamps();
    }

    public function isInterestedIn(User $user): bool
    {
        return $this->interestsGiven()->whereKey($user->id)->exists();
    }

    public function isMatchedWith(User $user): bool
    {
        return $this->isInterestedIn($user) && $user->isInterestedIn($this);
    }

    /** Membres avec qui l'intérêt est réciproque (matchs). */
    public function matchedUsers()
    {
        return $this->interestsGiven()->whereIn('users.id', function ($q) {
            $q->select('user_id')->from('interests')->where('target_user_id', $this->id);
        });
    }

    /* ---- Vues de profil (qui a vu mon profil) ---- */
    public function profileVisitors()
    {
        return $this->belongsToMany(User::class, 'profile_views', 'viewed_id', 'viewer_id')
            ->withTimestamps()
            ->orderByPivot('updated_at', 'desc');
    }

    /** Enregistre (ou rafraîchit) une vue de $target par cet utilisateur. */
    public function recordViewOf(User $target): void
    {
        if ($target->id === $this->id) {
            return;
        }
        ProfileView::updateOrCreate(
            ['viewer_id' => $this->id, 'viewed_id' => $target->id],
            ['updated_at' => now()],
        );
    }

    /* ---- Abonnements (qui me suit) ---- */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->whereKey($user->id)->exists();
    }

    /* ---- Demandes d'ami / amis ---- */
    public function friendRequestsSent()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function friendRequestsReceived()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    /** Identifiants des amis (demandes acceptées, dans les deux sens). */
    public function friendIds(): \Illuminate\Support\Collection
    {
        return FriendRequest::where('status', 'accepted')
            ->where(fn ($q) => $q->where('sender_id', $this->id)->orWhere('receiver_id', $this->id))
            ->get()
            ->map(fn ($r) => $r->sender_id === $this->id ? $r->receiver_id : $r->sender_id);
    }

    /** Requête des utilisateurs amis. */
    public function friends()
    {
        return User::whereIn('id', $this->friendIds());
    }

    public function isFriendWith(User $user): bool
    {
        return $this->friendIds()->contains($user->id);
    }

    /** Statut relationnel : none | pending_sent | pending_received | friends. */
    public function friendStatusWith(User $user): string
    {
        $req = FriendRequest::where(function ($q) use ($user) {
            $q->where('sender_id', $this->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $this->id);
        })->first();

        if (! $req) {
            return 'none';
        }
        if ($req->status === 'accepted') {
            return 'friends';
        }

        return $req->sender_id === $this->id ? 'pending_sent' : 'pending_received';
    }

    /* ---- Blocages ---- */

    /** Membres que CET utilisateur a bloqués. */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_id', 'blocked_id')->withTimestamps();
    }

    /** Membres qui ont bloqué CET utilisateur. */
    public function blockedByUsers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'blocker_id')->withTimestamps();
    }

    /** A-t-il bloqué $user ? */
    public function hasBlocked(User $user): bool
    {
        return $this->blockedUsers()->where('blocked_id', $user->id)->exists();
    }

    /** Est-il bloqué par $user, ou l'a-t-il bloqué ? (relation coupée dans les deux sens) */
    public function isBlockRelatedTo(User $user): bool
    {
        return \DB::table('blocks')
            ->where(fn ($q) => $q->where('blocker_id', $this->id)->where('blocked_id', $user->id))
            ->orWhere(fn ($q) => $q->where('blocker_id', $user->id)->where('blocked_id', $this->id))
            ->exists();
    }

    /** IDs de tous les membres en relation de blocage (dans les deux sens), pour filtrer les listes. */
    public function blockRelatedIds(): array
    {
        $out = \DB::table('blocks')->where('blocker_id', $this->id)->pluck('blocked_id');
        $in  = \DB::table('blocks')->where('blocked_id', $this->id)->pluck('blocker_id');

        return $out->merge($in)->unique()->values()->all();
    }

    /* ---- Abonnements / boosts ---- */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->latest();
    }

    public function boosts()
    {
        return $this->hasMany(Boost::class)->latest();
    }

    /** Abonnement premium actif (le plus récent encore valide). */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->active()
            ->with('plan')
            ->first();
    }

    /**
     * Membre premium, optimisé pour les listes : si la relation `subscriptions.plan`
     * est déjà chargée (eager-load), on l'utilise sans nouvelle requête (anti N+1).
     */
    public function isPremiumMember(): bool
    {
        if ($this->relationLoaded('subscriptions')) {
            return $this->subscriptions->contains(
                fn ($s) => $s->isCurrentlyActive() && ($s->plan?->is_premium)
            );
        }

        return $this->hasActiveSubscription();
    }

    /** A un abonnement premium en cours de validité ? */
    public function hasActiveSubscription(): bool
    {
        $sub = $this->activeSubscription();

        return $sub !== null && (bool) ($sub->plan?->is_premium);
    }

    /** Profil actuellement boosté (mis en avant) ? */
    public function isBoosted(): bool
    {
        return $this->boosts()->active()->exists();
    }

    /* ---- Notifications ---- */
    public function notifications()
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    /** Compteurs pour le navbar. */
    public function navCounts(): array
    {
        $unread = $this->notifications()->whereNull('read_at')->get(['type']);

        return [
            'notifs'   => $unread->where('type', '!=', 'message')->count(),
            'messages' => $unread->where('type', 'message')->count(),
            'friends'  => $this->friendRequestsReceived()->where('status', 'pending')->count(),
        ];
    }

    /** Récupère le profil ou en crée un vide. */
    public function profileOrNew(): Profile
    {
        return $this->profile()->firstOrCreate([]);
    }

    /* ---- Administration ---- */
    public const ROLES = [
        'super_admin' => 'Super administrateur',
        'moderateur'  => 'Modérateur',
    ];

    public const STATUS_LABELS = [
        'active'    => 'Actif',
        'suspended' => 'Suspendu',
        'banned'    => 'Banni',
    ];

    public function isAdminUser(): bool
    {
        return $this->role !== null;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Compte actuellement bloqué (banni, ou suspendu et toujours dans la fenêtre de suspension). */
    public function isBlocked(): bool
    {
        if ($this->status === 'banned') {
            return true;
        }

        return $this->status === 'suspended'
            && (! $this->suspended_until || $this->suspended_until->isFuture());
    }
}
