<?php

namespace App\Support;

use App\Mail\NotificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class Notifier
{
    /**
     * Envoie un e-mail transactionnel de façon sûre : un échec d'envoi
     * (SMTP indisponible, etc.) ne doit jamais interrompre l'action métier.
     *
     * @param  array<int,string>  $lines
     */
    public static function email(User $user, string $subject, string $heading, array $lines = [], ?string $ctaLabel = null, ?string $ctaUrl = null): void
    {
        // Respecte le consentement du membre (RGPD) et l'absence d'adresse.
        if (! $user->email || ! $user->email_opt_in) {
            return;
        }

        try {
            Mail::to($user->email)->send(new NotificationMail($subject, $heading, $lines, $ctaLabel, $ctaUrl));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
