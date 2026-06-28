<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Voir ses visiteurs devient réservé aux abonnés Premium.
        \App\Models\Setting::put('premium.premium_required_see_visitors', '1', 'bool', 'premium');
    }

    public function down(): void
    {
        \App\Models\Setting::put('premium.premium_required_see_visitors', '0', 'bool', 'premium');
    }
};
