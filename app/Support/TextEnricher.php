<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TextEnricher
{
    private const MENTION_PATTERN = '/@([A-Za-zÀ-ÖØ-öø-ÿ_]{2,40})/u';
    private const HASHTAG_PATTERN = '/#([A-Za-zÀ-ÖØ-öø-ÿ0-9_]{2,30})/u';

    /** Mémo par requête : nom (minuscule) => User|null — évite de recharger toute la table. */
    private static array $nameCache = [];

    /** Échappe le texte puis transforme les #hashtags et @mentions en liens. */
    public static function render(string $text): string
    {
        $authed = auth()->check();
        $tokens = [];

        // Remplace d'abord les correspondances par des jetons neutres (avant échappement),
        // pour éviter que le regex hashtag ne matche des entités HTML comme &#039;.
        $marked = preg_replace_callback(self::HASHTAG_PATTERN, function ($m) use (&$tokens) {
            $key = "\x00H" . count($tokens) . "\x00";
            $tokens[$key] = '<a href="' . route('communaute', ['tag' => $m[1]]) . '" class="tag-link">#' . e($m[1]) . '</a>';

            return $key;
        }, $text);

        $marked = preg_replace_callback(self::MENTION_PATTERN, function ($m) use (&$tokens, $authed) {
            $user = self::findUserByName(str_replace('_', ' ', $m[1]));
            if (! $user) {
                return $m[0];
            }

            $key = "\x00M" . count($tokens) . "\x00";
            $tokens[$key] = $authed
                ? '<a href="' . route('members.show', $user) . '" class="mention-link">@' . e($m[1]) . '</a>'
                : '<span class="mention-link">@' . e($m[1]) . '</span>';

            return $key;
        }, $marked);

        $html = e($marked);

        return strtr($html, $tokens);
    }

    /** Membres mentionnés (@nom) dans un texte brut. */
    public static function mentionedUsers(string $text): Collection
    {
        preg_match_all(self::MENTION_PATTERN, $text, $matches);
        $names = collect($matches[1] ?? [])->map(fn ($n) => str_replace('_', ' ', $n))->unique();

        if ($names->isEmpty()) {
            return collect();
        }

        return $names->map(fn ($n) => self::findUserByName($n))->filter()->unique('id')->values();
    }

    private static function findUserByName(string $name): ?User
    {
        $key = Str::lower(trim($name));

        if (array_key_exists($key, self::$nameCache)) {
            return self::$nameCache[$key];
        }

        return self::$nameCache[$key] = User::whereRaw('LOWER(name) = ?', [$key])->first();
    }
}
