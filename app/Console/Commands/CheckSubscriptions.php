<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Setting;
use App\Models\Subscription;
use App\Support\Notifier;
use Illuminate\Console\Command;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';

    protected $description = "Expire les abonnements échus et envoie les rappels d'expiration (J-3, J-1).";

    public function handle(): int
    {
        $now = now();

        // 1) Bascule automatique des abonnements échus en « expiré ».
        $expired = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->update(['status' => 'expired']);
        $this->info("Abonnements expirés : {$expired}");

        // 2) Rappels avant échéance (une seule fois chacun).
        $sent3 = $this->sendReminders('reminder_3d_at', $now->copy()->addDays(3), 3);
        $sent1 = $this->sendReminders('reminder_1d_at', $now->copy()->addDay(), 1);
        $this->info("Rappels envoyés — J-3 : {$sent3} · J-1 : {$sent1}");

        return self::SUCCESS;
    }

    /** Envoie un rappel aux abonnements actifs expirant avant $threshold et non encore relancés. */
    private function sendReminders(string $flag, \Illuminate\Support\Carbon $threshold, int $days): int
    {
        $count = 0;

        $subs = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->where('ends_at', '<=', $threshold)
            ->whereNull($flag)
            ->get();

        foreach ($subs as $sub) {
            if (! $sub->user) {
                continue;
            }

            $when = $sub->ends_at->locale('fr')->isoFormat('D MMMM YYYY');

            Notifier::email(
                $sub->user,
                'Votre abonnement Premium expire bientôt ⏳',
                'Pensez à renouveler',
                [
                    'Votre abonnement ' . ($sub->plan?->name ?? 'Premium') . ' sur ' . Setting::siteName() . " arrive à échéance le {$when}.",
                    $days === 1 ? 'Il vous reste moins de 24 h.' : 'Il vous reste quelques jours.',
                    "Renouvelez pour continuer à envoyer des demandes d'amis et à discuter librement.",
                ],
                'Renouveler mon abonnement',
                route('tarifs'),
            );

            AppNotification::record(
                $sub->user_id,
                null,
                'subscription',
                "Votre abonnement Premium expire le {$when}. Pensez à renouveler ✨",
                route('subscription.mine'),
            );

            $sub->forceFill([$flag => now()])->save();
            $count++;
        }

        return $count;
    }
}
