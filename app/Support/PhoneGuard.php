<?php

namespace App\Support;

/**
 * Détecte les tentatives de partage de numéros de téléphone dans un message,
 * y compris les contournements courants :
 *  - chiffres collés ou espacés : « 771588903 », « 77 158 89 03 », « 7.7.1.5... »
 *  - chiffres écrits en toutes lettres : « sept sept un cinq huit... »
 *  - substitutions de lettres : O→0, l/i→1, e→3, s→5, b→8...
 *  - numéro découpé et envoyé « par petits lots » sur plusieurs messages
 *    (« mon num c'est » → « 77 » → « 158 » → « 89 03 ») : voir assembledPhone().
 *
 * Heuristique volontairement prudente : on ne bloque que si, après normalisation,
 * une suite suffisamment longue de chiffres apparaît (un numéro sénégalais en
 * compte 9). Une phrase normale ne produit pas une telle suite.
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

    /** Longueur minimale d'une suite de chiffres considérée comme un numéro. */
    private const MIN_RUN = 7;

    /** Chiffres minimaux accumulés sur plusieurs messages pour bloquer. */
    private const MIN_ASSEMBLED = 8;

    /** Détecte un numéro dans UN message (toutes les variantes ci-dessus). */
    public static function containsPhone(string $text): bool
    {
        $base = self::normalizeWords($text);

        // On teste deux variantes : sans et avec substitution « leet ».
        foreach ([$base, strtr($base, self::LEET)] as $candidate) {
            // Colle les chiffres séparés par jusqu'à 3 séparateurs (espaces, points, tirets…)
            $joined = preg_replace('/(\d)[\s.\-_,;:\/\\\\]{0,3}(?=\d)/u', '$1', $candidate);
            if (preg_match('/\d{' . self::MIN_RUN . ',}/', $joined)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Détecte un numéro ASSEMBLÉ sur plusieurs messages.
     * On ne retient que les messages « fragments de chiffres » (courts et
     * majoritairement numériques), on concatène leurs chiffres avec le nouveau
     * message, et on bloque si l'accumulation atteint la longueur d'un numéro.
     *
     * @param  array<int,string>  $previousBodies  messages récents de l'expéditeur
     */
    public static function assembledPhone(array $previousBodies, string $newBody): bool
    {
        $buffer = '';

        foreach (array_merge($previousBodies, [$newBody]) as $body) {
            if (self::isDigitFragment($body)) {
                $buffer .= self::digitsOnly($body);
            }
        }

        return strlen($buffer) >= self::MIN_ASSEMBLED;
    }

    /** Un message est-il un « fragment de chiffres » (peu de texte, surtout des chiffres) ? */
    private static function isDigitFragment(string $text): bool
    {
        $norm = self::normalizeWords($text);
        // On retire séparateurs et indicatifs courants pour juger du contenu réel
        $stripped = preg_replace('/[\s.\-_,;:\/\\\\()+]/u', '', $norm);

        if ($stripped === '' || $stripped === null) {
            return false;
        }

        $digitCount = preg_match_all('/\d/', $stripped);

        // Au moins 2 chiffres ET les chiffres représentent au moins la moitié du contenu utile.
        return $digitCount >= 2 && $digitCount >= mb_strlen($stripped) * 0.5;
    }

    /** Chiffres seuls d'un message (après conversion des chiffres écrits en lettres). */
    private static function digitsOnly(string $text): string
    {
        return preg_replace('/\D+/', '', self::normalizeWords($text));
    }

    /** Minuscule + conversion des chiffres écrits en toutes lettres → chiffres. */
    private static function normalizeWords(string $text): string
    {
        $base = ' ' . mb_strtolower($text, 'UTF-8') . ' ';

        return preg_replace_callback(
            '/\b(zéro|zero|une?|deux|trois|quatre|cinq|six|sept|huit|neuf)\b/u',
            fn ($m) => self::WORD_DIGITS[$m[1]] ?? $m[0],
            $base
        );
    }
}
