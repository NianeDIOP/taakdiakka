<?php
/**
 * Export statique du site Laravel vers /dist (hébergement gratuit :
 * Netlify / Cloudflare Pages / GitHub Pages).
 *
 * Pré-requis : le serveur doit tourner →  php artisan serve
 * Usage      :  php scripts/export-static.php
 */

$base = 'http://127.0.0.1:8000';
$root = dirname(__DIR__);
$dist = $root . '/dist';

// Bootstrap Laravel (pour récupérer de vrais identifiants en base)
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$demandeId = optional(\App\Models\Demande::orderBy('id')->first())->id;
$postId    = optional(\App\Models\Post::orderBy('id')->first())->id;

// Routes publiques à figer
$routes = array_values(array_filter([
    '/',
    '/demandes',
    $demandeId ? "/demandes/{$demandeId}" : null,
    '/communaute',
    $postId ? "/communaute/{$postId}" : null,
    '/histoires',
    '/tarifs',
    '/connexion',
    '/inscription',
]));

// Bandeau « aperçu statique » injecté avant </body>
$banner = '<div style="position:fixed;left:0;right:0;bottom:0;z-index:99999;background:#1a1712;color:#f7f3ea;'
        . 'font-family:system-ui,sans-serif;font-size:.72rem;letter-spacing:.12em;text-transform:uppercase;'
        . 'text-align:center;padding:9px;border-top:1px solid #caa552">'
        . 'Aperçu statique — démonstration · TàakDiàkka ❤</div>';

@mkdir($dist, 0777, true);

$ok = 0;
foreach ($routes as $route) {
    $file = $route === '/' ? 'index.html' : trim($route, '/') . '/index.html';
    $html = @file_get_contents($base . $route);
    if ($html === false) {
        fwrite(STDERR, "  ⚠ ignorée (serveur ?) : {$route}\n");
        continue;
    }
    $html = str_replace($base . '/', '/', $html);
    $html = str_replace($base, '', $html);
    $html = str_replace('</body>', $banner . "\n</body>", $html);

    $target = $dist . '/' . $file;
    @mkdir(dirname($target), 0777, true);
    file_put_contents($target, $html);
    echo "page  {$route}  ->  dist/{$file}\n";
    $ok++;
}

if ($ok === 0) {
    fwrite(STDERR, "\nAucune page exportée. Le serveur tourne-t-il ?  php artisan serve\n");
    exit(1);
}

// Copier les assets statiques
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

file_put_contents($dist . '/.nojekyll', '');

echo "\nExport terminé ({$ok} pages) → dossier dist/\n";
