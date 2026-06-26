<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Optimisation d'images (GD) : redimensionne et génère WebP + JPG dans public/img.
 * Retourne le nom de fichier (.jpg) à stocker, ou null en cas d'échec.
 */
class ImageOptimizer
{
    public static function fromUpload(UploadedFile $file, string $prefix, int $maxW = 760): ?string
    {
        $img = @match ($file->getMimeType()) {
            'image/png'  => imagecreatefrompng($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default      => imagecreatefromjpeg($file->getPathname()),
        };
        if (! $img) {
            return null;
        }
        [$w, $h] = getimagesize($file->getPathname());

        return self::write($img, $w, $h, $prefix, $maxW);
    }

    public static function fromBase64(string $dataUrl, string $prefix, int $maxW = 760): ?string
    {
        $binary = base64_decode(preg_replace('#^data:image/\w+;base64,#', '', $dataUrl), true);
        if ($binary === false) {
            return null;
        }
        $img = @imagecreatefromstring($binary);
        if (! $img) {
            return null;
        }
        $size = @getimagesizefromstring($binary);
        if (! $size) {
            return null;
        }

        return self::write($img, $size[0], $size[1], $prefix, $maxW);
    }

    private static function write(\GdImage $src, int $w, int $h, string $prefix, int $maxW): string
    {
        $nw = min($maxW, $w);
        $nh = (int) round($h * $nw / $w);
        $dst = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $base = $prefix . '-' . now()->timestamp . '-' . Str::lower(Str::random(4));
        $dir = public_path('img');
        imagewebp($dst, "{$dir}/{$base}.webp", 80);
        imagejpeg($dst, "{$dir}/{$base}.jpg", 82);

        imagedestroy($src);
        imagedestroy($dst);

        return $base . '.jpg';
    }

    /** Supprime les fichiers .webp/.jpg associés à un nom de base. */
    public static function delete(string $path): void
    {
        $base = pathinfo($path, PATHINFO_FILENAME);
        @unlink(public_path("img/{$base}.webp"));
        @unlink(public_path("img/{$base}.jpg"));
    }
}
