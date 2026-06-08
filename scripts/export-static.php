<?php
/**
 * Export statique du site Laravel vers /dist (pour hébergement gratuit type
 * Cloudflare Pages / Netlify / GitHub Pages).
 *
 * Pré-requis : le serveur doit tourner →  php artisan serve
 * Usage      :  php scripts/export-static.php
 */

$base = 'http://127.0.0.1:8000';
$root = dirname(__DIR__);
$dist = $root . '/dist';

// Pages à figer : route => fichier de sortie
$pages = [
    '/'         => 'index.html',
    '/demandes' => 'demandes/index.html',
];

// Bandeau « aperçu statique » injecté avant </body>
$banner = '<div style="position:fixed;left:0;right:0;bottom:0;z-index:99999;background:#1a1712;color:#f7f3ea;'
        . 'font-family:system-ui,sans-serif;font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;'
        . 'text-align:center;padding:9px;border-top:1px solid #caa552">'
        . 'Aperçu statique — démonstration · TàakDiàkka ❤</div>';

@mkdir($dist, 0777, true);

// 1) Figer les pages HTML
foreach ($pages as $route => $file) {
    $html = @file_get_contents($base . $route);
    if ($html === false) {
        fwrite(STDERR, "ERREUR : serveur introuvable sur {$base}. Lancez d'abord : php artisan serve\n");
        exit(1);
    }
    // URLs absolues -> relatives à la racine
    $html = str_replace($base . '/', '/', $html);
    $html = str_replace($base, '', $html);
    // Injecter le bandeau
    $html = str_replace('</body>', $banner . "\n</body>", $html);

    $target = $dist . '/' . $file;
    @mkdir(dirname($target), 0777, true);
    file_put_contents($target, $html);
    echo "page  {$route}  ->  dist/{$file}\n";
}

// 2) Copier les assets statiques
function rcopy(string $s, string $d): void
{
    if (is_dir($s)) {
        @mkdir($d, 0777, true);
        foreach (scandir($s) as $f) {
            if ($f === '.' || $f === '..') continue;
            rcopy("$s/$f", "$d/$f");
        }
    } else {
        copy($s, $d);
    }
}
foreach (['css', 'js', 'img'] as $a) {
    rcopy("$root/public/$a", "$dist/$a");
    echo "assets  /{$a}\n";
}

// 3) Fichiers utiles à l'hébergement statique
file_put_contents($dist . '/.nojekyll', '');                 // GitHub Pages

echo "\nExport terminé → dossier dist/\n";
