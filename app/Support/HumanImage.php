<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Détection « l'image contient-elle un visage humain ? » côté serveur,
 * sans service externe (détecteur Haar pur PHP).
 *
 * Principe de prudence : on ne REJETTE que si la détection s'est déroulée
 * sans erreur ET qu'aucun visage n'a été trouvé. En cas d'échec du détecteur,
 * on accepte (on ne bloque jamais un membre à cause d'un bug technique).
 */
class HumanImage
{
    public static function uploadHasFace(UploadedFile $file): bool
    {
        return self::hasFace($file->getPathname());
    }

    /** Vérifie une image envoyée en data-URI base64 (capture caméra). */
    public static function base64HasFace(?string $dataUri): bool
    {
        if (! $dataUri || ! preg_match('#^data:image/\w+;base64,#', $dataUri)) {
            return true;
        }
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $dataUri), true);
        if ($data === false) {
            return true;
        }
        $tmp = tempnam(sys_get_temp_dir(), 'td_b64');
        file_put_contents($tmp, $data);
        $found = self::hasFace($tmp);
        @unlink($tmp);

        return $found;
    }

    public static function hasFace(string $sourcePath): bool
    {
        $data = @file_get_contents($sourcePath);
        if ($data === false) {
            return true; // illisible ici : on laisse passer, la validation image standard gère le reste
        }

        $img = @imagecreatefromstring($data);
        if (! $img) {
            return true;
        }

        // Réduit la taille (vitesse + mémoire) puis écrit un JPEG temporaire
        $img = self::downscale($img, 900);
        $tmp = tempnam(sys_get_temp_dir(), 'td_face') . '.jpg';
        @imagejpeg($img, $tmp, 90);
        imagedestroy($img);

        $found = true;
        $prev = error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
        $prevLimit = (int) ini_get('max_execution_time');
        @set_time_limit(0); // exécuté uniquement en file d'attente (hors requête HTTP)
        try {
            $datFile = base_path('vendor/mauricesvay/php-facedetection/detection.dat');
            $detector = new FastFaceDetector($datFile);
            $detector->minFaceRatio = 0.20; // le visage doit occuper ≥ 20 % de la photo (profil net)
            $found = (bool) $detector->faceDetect($tmp);
        } catch (\Throwable $e) {
            report($e);
            $found = true; // erreur du détecteur → ne pas bloquer
        } finally {
            error_reporting($prev);
            @set_time_limit($prevLimit ?: 30);
            @unlink($tmp);
        }

        return $found;
    }

    /** @param \GdImage $img */
    private static function downscale($img, int $max)
    {
        $w = imagesx($img);
        $h = imagesy($img);
        if ($w <= $max && $h <= $max) {
            return $img;
        }
        $ratio = $w >= $h ? $max / $w : $max / $h;
        $nw = max(1, (int) round($w * $ratio));
        $nh = max(1, (int) round($h * $ratio));
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);

        return $dst;
    }
}
