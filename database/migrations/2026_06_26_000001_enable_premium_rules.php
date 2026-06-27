<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Active le modèle premium et les règles voulues.
        \App\Models\Setting::put('premium.enforced', '1', 'bool', 'premium');
        \App\Models\Setting::put('premium.premium_required_friend_request', '1', 'bool', 'premium');
        \App\Models\Setting::put('premium.free_messages_per_contact', '0', 'int', 'premium');
    }

    public function down(): void
    {
        \App\Models\Setting::put('premium.enforced', '0', 'bool', 'premium');
    }
};
