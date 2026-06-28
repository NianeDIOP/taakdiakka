<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_device', 20)->nullable()->after('last_seen_at');
            $table->string('last_browser', 40)->nullable()->after('last_device');
            $table->string('last_ip', 45)->nullable()->after('last_browser');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_device', 'last_browser', 'last_ip']);
        });
    }
};
