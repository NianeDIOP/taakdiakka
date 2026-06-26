<?php

namespace App\Support;

use svay\FaceDetector;

/**
 * Variante rapide du détecteur Haar pour la validation de photos de PROFIL :
 * on n'évalue que les visages suffisamment grands (≥ ~25 % de l'image), ce qui
 * évite le balayage très coûteux des petites fenêtres. Une photo de profil
 * montre toujours un visage proche ; les images sans visage sont rejetées en
 * quelques secondes au lieu de ~50 s.
 */
class FastFaceDetector extends FaceDetector
{
    /** Fraction minimale de l'image que doit occuper le visage. */
    public float $minFaceRatio = 0.25;

    protected function doDetectGreedyBigToSmall($ii, $ii2, $width, $height)
    {
        $s_w = $width / 20.0;
        $s_h = $height / 20.0;
        $start_scale = $s_h < $s_w ? $s_h : $s_w;
        $scale_update = 1 / 1.2;
        $min_w = min($width, $height) * $this->minFaceRatio;

        for ($scale = $start_scale; $scale > 1; $scale *= $scale_update) {
            $w = (20 * $scale) >> 0;
            if ($w < $min_w) {
                break; // visages trop petits ignorés → rapide
            }
            $endx = $width - $w - 1;
            $endy = $height - $w - 1;
            $step = max($scale, 2) >> 0;
            $inv_area = 1 / ($w * $w);
            for ($y = 0; $y < $endy; $y += $step) {
                for ($x = 0; $x < $endx; $x += $step) {
                    if ($this->detectOnSubImage($x, $y, $scale, $ii, $ii2, $w, $width + 1, $inv_area)) {
                        return ['x' => $x, 'y' => $y, 'w' => $w];
                    }
                }
            }
        }

        return null;
    }
}
