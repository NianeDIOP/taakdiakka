<?php

namespace App\Support;

/**
 * Complément de PhoneGuard : détecte le partage de coordonnées « hors plateforme »
 * autres qu'un numéro de téléphone — adresses e-mail et invitations vers des
 * réseaux / messageries externes (WhatsApp, Instagram, Snapchat, Telegram…).
 *
 * Objectif : empêcher de quitter la plateforme avant d'avoir vraiment fait
 * connaissance (même règle que PhoneGuard : autorisé seulement après > 10 messages).
 *
 * On ne bloque que sur des signaux forts (vraie adresse, e-mail obfusqué, pseudo
 * @handle, ou invitation explicite « ajoute-moi sur … ») afin d'éviter les faux
 * positifs : poser une question (« tu as Instagram ? ») n'est PAS bloqué.
 */
class ContactGuard
{
    /** Plateformes / messageries externes courantes (avec fautes fréquentes). */
    private const PLATFORMS = [
        'whatsapp', 'whatsap', 'whatsup', 'wassap', 'watsap', 'wsp', 'wtsp',
        'snap', 'snapchat', 'instagram', 'insta', 'telegram', 'tiktok', 'tik tok',
        'viber', 'signal', 'imo', 'facebook', 'messenger', 'fb', 'skype', 'discord',
        'gmail', 'yahoo', 'hotmail', 'outlook', 'icloud',
    ];

    /** Verbes/locutions d'invitation à se contacter ailleurs. */
    private const INVITES = [
        'ajoute', 'ajoutes', 'ajt', 'écris', 'ecris', 'contacte', 'appelle',
        'rejoins', 'retrouve', 'suis', 'follow', 'add', 'dm', 'mp',
    ];

    public static function containsContact(string $text): bool
    {
        $t = mb_strtolower($text, 'UTF-8');

        // 1) Adresse e-mail directe : nom@domaine.tld
        if (preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $t)) {
            return true;
        }

        // 2) E-mail obfusqué : « nom at gmail dot com », « nom(at)gmail.com », « nom arobase … point com »
        if (preg_match('/[a-z0-9._\-]{2,}\s*(\(at\)|\[at\]|\bat\b|arobase|@)\s*[a-z0-9._\-]{2,}\s*(\.|\bdot\b|\bpoint\b)\s*(com|fr|net|org|sn|co)\b/iu', $t)) {
            return true;
        }

        $platforms = '(' . implode('|', array_map('preg_quote', self::PLATFORMS)) . ')';
        $hasPlatform = (bool) preg_match('/\b' . $platforms . '\b/iu', $t);

        // 3) Pseudo @handle (3+ caractères) → quasi toujours une coordonnée
        if (preg_match('/(?<![a-z0-9])@[a-z0-9._]{3,}/i', $t)) {
            return true;
        }

        if ($hasPlatform) {
            // 4) Plateforme suivie d'une valeur : « insta : lily_dakar », « snap = xyz », « mon wsp c'est … »
            if (preg_match('/\b' . $platforms . '\b[^\n]{0,20}([:=]|c[\'’ ]?est|cest|->|:)\s*\S{3,}/iu', $t)) {
                return true;
            }

            // 5) Invitation explicite à se contacter ailleurs : « ajoute-moi sur whatsapp »
            $invites = '(' . implode('|', array_map('preg_quote', self::INVITES)) . ')';
            if (preg_match('/\b' . $invites . '\b[^\n]{0,30}\b' . $platforms . '\b/iu', $t)) {
                return true;
            }
        }

        return false;
    }
}
