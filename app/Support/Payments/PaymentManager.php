<?php

namespace App\Support\Payments;

use App\Models\Setting;

class PaymentManager
{
    /**
     * Renvoie la passerelle active. Si PayDunya est choisi et correctement
     * configuré, on l'utilise ; sinon on retombe sur la démonstration.
     */
    public static function gateway(): PaymentGateway
    {
        $provider = Setting::get('payment.provider', 'stub');

        if ($provider === 'paydunya') {
            $paydunya = new PaydunyaGateway();
            if ($paydunya->isConfigured()) {
                return $paydunya;
            }
        }

        return new StubGateway();
    }

    /** Mode démonstration actif (aucun vrai encaissement) ? */
    public static function isDemo(): bool
    {
        return self::gateway()->key() === 'stub';
    }
}
