<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // En production : forcer les URLs en HTTPS (canonical, Open Graph, sitemap, assets)
        if ($this->app->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $this->applyMailSettings();

        // Partage les IDs des demandes mises en favori (mémoïsé : 1 requête / requête HTTP)
        View::composer('*', function ($view) {
            static $ids = null;
            if ($ids === null) {
                $ids = Auth::check()
                    ? Auth::user()->favorites()->pluck('demandes.id')->all()
                    : [];
            }
            $view->with('favoriteIds', $ids);
        });

        // Compteurs du navbar membre (notifications, messages, demandes d'ami)
        View::composer('layouts.member', function ($view) {
            $counts = Auth::check() ? Auth::user()->navCounts() : ['notifs' => 0, 'messages' => 0, 'friends' => 0];
            $view->with('navUnreadNotifs', $counts['notifs'])
                ->with('navUnreadMessages', $counts['messages'])
                ->with('navPendingFriends', $counts['friends']);
        });

        // Badge "signalements en attente" pour la sidebar admin
        View::composer('layouts.admin', function ($view) {
            $view->with('admPendingReports', \App\Models\Report::where('status', 'pending')->count());
        });
    }

    /**
     * Applique au runtime les réglages e-mail définis depuis l'admin (table settings),
     * sans nécessiter de redéploiement. Si aucun SMTP n'est configuré, on garde le
     * mailer par défaut (.env) — typiquement « log » en développement.
     */
    private function applyMailSettings(): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable $e) {
            return; // base non prête (ex. avant migration)
        }

        $fromEmail = \App\Models\Setting::get('mail.from_email');
        $fromName  = \App\Models\Setting::get('mail.from_name') ?: \App\Models\Setting::siteName();

        if ($fromEmail) {
            config(['mail.from.address' => $fromEmail, 'mail.from.name' => $fromName]);
        }

        $host = \App\Models\Setting::get('mail.host');
        if (! $host) {
            return; // pas de SMTP : on laisse le mailer du .env
        }

        $enc = \App\Models\Setting::get('mail.encryption', 'tls');
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host'       => $host,
            'mail.mailers.smtp.port'       => (int) (\App\Models\Setting::get('mail.port') ?: 587),
            'mail.mailers.smtp.username'   => \App\Models\Setting::get('mail.username') ?: null,
            'mail.mailers.smtp.password'   => \App\Models\Setting::get('mail.password') ?: null,
            'mail.mailers.smtp.encryption' => $enc === 'none' ? null : $enc,
        ]);
    }
}
