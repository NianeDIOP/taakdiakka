<?php

namespace App\Support;

/**
 * Détecte les tentatives de partage de numéros de téléphone dans un message,
 * y compris les contournements courants :
 *  - chiffres collés ou espacés : « 771588903 », « 77 158 89 03 », « 7.7.1.5... »
 *  - chiffres écrits en toutes lettres : « sept sept un cinq huit... »
 *  - substitutions de lettres : O→0, l/i→1, e→3, s→5, b→8...
 *
 * Heuristique volontairement prudente : on ne bloque que si, après normalisation,
 * une suite d'au moins 7 chiffres consécutifs apparaît (un numéro sénégalais en
 * compte 9). Une phrase normale ne produit pas 7 chiffres d'affilée.
 */
class PhoneGuard
{
    private const WORD_DIGITS = [
        'zéro' => '0', 'zero' => '0', 'un' => '1', 'une' => '1', 'deux' => '2',
        'trois' => '3', 'quatre' => '4', 'cinq' => '5', 'six' => '6', 'sept' => '7',
        'huit' => '8', 'neuf' => '9',
    ];

    private const LEET = [
        'o' => '0', 'l' => '1', 'i' => '1', 'e' => '3', 's' => '5', 'b' => '8', 'g' => '9', 'q' => '9', 'z' => '2',
    ];

    public static function containsPhone(string $text): bool
    {
        $base = ' ' . mb_strtolower($text, 'UTF-8') . ' ';

        // 1) chiffres écrits en toutes lettres -> chiffres
        $base = preg_replace_callback(
            '/\b(zéro|zero|une?|deux|trois|quatre|cinq|six|sept|huit|neuf)\b/u',
            fn ($m) => self::WORD_DIGITS[$m[1]] ?? $m[0],
            $base
        );

        // On teste deux variantes : sans et avec substitution « leet ».
        $candidates = [$base, strtr($base, self::LEET)];

        foreach ($candidates as $candidate) {
            // Colle les chiffres séparés par jusqu'à 3 séparateurs (espaces, points, tirets…)
            $joined = preg_replace('/(\d)[\s.\-_,;:\/\\\\]{0,3}(?=\d)/u', '$1', $candidate);
            if (preg_match('/\d{7,}/', $joined)) {
                return true;
            }
        }

        return false;
    }
}
