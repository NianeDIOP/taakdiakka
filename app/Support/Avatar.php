<?php

namespace App\Support;

class Avatar
{
    private const WOMEN = ['profil-w1', 'profil-w2', 'profil-w3', 'profil-w4'];
    private const MEN   = ['profil-m1', 'profil-m2', 'profil-m3'];

    private const FEMALE_NAMES = [
        'fatou', 'maïmouna', 'khady', 'aïssatou', 'ndèye', 'rama', 'sokhna', 'khadija',
        'coumba', 'awa', 'bineta', 'adja', 'mariama', 'aïcha', 'aminata', 'mame', 'fatima', 'aida',
    ];
    private const MALE_NAMES = [
        'ibrahima', 'ousmane', 'cheikh', 'mamadou', 'abdou', 'pape', 'serigne', 'alioune',
        'modou', 'babacar', 'moussa', 'lamine', 'khalil', 'cheikhouna',
    ];

    /** Nom de fichier (sans extension) d'un portrait pour ce prénom, ou null. */
    public static function photo(?string $name): ?string
    {
        if (! $name) {
            return null;
        }

        $first = mb_strtolower(trim(strtok($name, ' ')));
        $h = crc32(mb_strtolower($name));

        if (in_array($first, self::FEMALE_NAMES, true)) {
            return self::WOMEN[$h % count(self::WOMEN)];
        }
        if (in_array($first, self::MALE_NAMES, true)) {
            return self::MEN[$h % count(self::MEN)];
        }

        $all = array_merge(self::WOMEN, self::MEN);

        return $all[$h % count($all)];
    }

    /** Initiales (repli). */
    public static function initials(?string $name): string
    {
        if (! $name) {
            return '?';
        }

        return mb_strtoupper(collect(explode(' ', $name))
            ->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')) ?: '?';
    }
}
