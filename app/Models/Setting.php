<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    public const CACHE_KEY = 'settings.all';

    /** Toutes les valeurs (clé => valeur typée), mémoïsées. */
    public static function all_cached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::all()->mapWithKeys(fn (Setting $s) => [$s->key => $s->castValue()])->all();
        });
    }

    /** Lit une valeur typée avec valeur par défaut. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all_cached()[$key] ?? $default;
    }

    /** Lit un drapeau booléen (vrai par défaut si la clé n'existe pas encore). */
    public static function enabled(string $key, bool $default = true): bool
    {
        $val = self::all_cached()[$key] ?? null;

        return $val === null ? $default : (bool) $val;
    }

    /** URL du logo personnalisé, ou le logo par défaut. */
    public static function logo(): string
    {
        $logo = self::get('site.logo');

        return $logo ? asset('img/' . $logo) : asset('img/logo-mark.png');
    }

    /** Nom du site (avec repli). */
    public static function siteName(): string
    {
        return self::get('site.name') ?: 'TàakDiàkka';
    }

    /** Écrit une valeur et vide le cache. */
    public static function put(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $stored = $type === 'json' ? json_encode($value) : (string) (is_bool($value) ? ($value ? 1 : 0) : $value);

        self::updateOrCreate(['key' => $key], ['value' => $stored, 'type' => $type, 'group' => $group]);
        Cache::forget(self::CACHE_KEY);
    }

    /** Écrit plusieurs valeurs d'un coup. */
    public static function putMany(array $pairs, string $group = 'general'): void
    {
        foreach ($pairs as $key => [$value, $type]) {
            self::put($key, $value, $type, $group);
        }
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue(): mixed
    {
        return match ($this->type) {
            'bool' => (bool) $this->value,
            'int'  => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
