<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->string('client_name', 100)->default('')->after('image');
            $table->decimal('price', 10, 0)->default(0)->after('client_name');
            $table->unsignedSmallInteger('duration_days')->default(30)->after('price');
            $table->timestamp('starts_at')->nullable()->after('duration_days');
            $table->timestamp('expires_at')->nullable()->after('starts_at');
            $table->string('notes', 400)->default('')->after('cta_label');
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'price', 'duration_days', 'starts_at', 'expires_at', 'notes']);
        });
    }
};
