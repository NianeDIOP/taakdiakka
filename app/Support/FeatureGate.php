<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\User;

/**
 * Point central de contrôle des fonctionnalités :
 *  - activation/désactivation des modules entiers (communauté, galerie…)
 *  - règles premium (ce que la version gratuite peut faire, et les limites)
 *
 * Toutes les valeurs sont configurables depuis l'admin via la table `settings`,
 * sans jamais toucher au code.
 */
class FeatureGate
{
    /** Modules activables/désactivables. clé => [label, défaut activé]. */
    public const MODULES = [
        'community'    => ['Communauté', true],
        'gallery'      => ['Galerie de photos', true],
        'verification' => ['Vérification d\'identité', true],
        'messaging'    => ['Messagerie', true],
        'boosts'       => ['Boosts de visibilité', true],
        'stories'      => ['Témoignages / success stories', true],
    ];

    /** Règles premium configurables. clé => [label, type, défaut]. */
    public const PREMIUM_RULES = [
        'premium_required_friend_request' => ['Demande d\'ami réservée aux abonnés', 'bool', true],
        'free_messages_per_contact'       => ['Messages gratuits par contact', 'int', 2],
        'free_photos_visible'             => ['Photos de galerie visibles (gratuit)', 'int', 1],
        'premium_required_see_visitors'   => ['Voir ses visiteurs réservé aux abonnés', 'bool', false],
    ];

    /* ---------------- Modules ---------------- */

    public static function moduleEnabled(string $module): bool
    {
        return Setting::enabled('module.' . $module, self::MODULES[$module][1] ?? true);
    }

    /* ---------------- Règles premium ---------------- */

    /**
     * Interrupteur maître de la monétisation. Tant qu'il est désactivé (défaut),
     * aucune restriction premium n'est appliquée : la plateforme est entièrement
     * ouverte. L'admin l'active une fois les abonnements & le paiement en place.
     */
    public static function monetizationEnabled(): bool
    {
        return Setting::enabled('premium.enforced', false);
    }

    public static function rule(string $key): mixed
    {
        $default = self::PREMIUM_RULES[$key][2] ?? null;

        return Setting::get('premium.' . $key, $default);
    }

    /** L'utilisateur a-t-il un accès premium actif ? (les admins passent toujours) */
    public static function isPremium(?User $user): bool
    {
        if (! $user) {
            return false;
        }
        if ($user->isAdminUser()) {
            return true;
        }

        // Branché sur les abonnements en Phase 3 ; sûr tant que la méthode n'existe pas encore.
        return method_exists($user, 'hasActiveSubscription') && $user->hasActiveSubscription();
    }

    /** Peut envoyer une demande d'ami ? */
    public static function canSendFriendRequest(?User $user): bool
    {
        if (! self::monetizationEnabled() || ! self::rule('premium_required_friend_request')) {
            return true;
        }

        return self::isPremium($user);
    }

    /** Peut voir ses visiteurs ? */
    public static function canSeeVisitors(?User $user): bool
    {
        if (! self::monetizationEnabled() || ! self::rule('premium_required_see_visitors')) {
            return true;
        }

        return self::isPremium($user);
    }

    /** Nombre de messages gratuits autorisés par contact (PHP_INT_MAX si non restreint). */
    public static function messagesPerContact(?User $user): int
    {
        if (! self::monetizationEnabled() || self::isPremium($user)) {
            return PHP_INT_MAX;
        }

        return (int) self::rule('free_messages_per_contact');
    }

    /** Nombre de photos de galerie visibles (PHP_INT_MAX si non restreint). */
    public static function visiblePhotos(?User $user): int
    {
        if (! self::monetizationEnabled() || self::isPremium($user)) {
            return PHP_INT_MAX;
        }

        return (int) self::rule('free_photos_visible');
    }
}
