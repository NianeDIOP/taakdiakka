<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('is_admin'); // null | moderateur | super_admin
            $table->string('status')->default('active')->after('role'); // active | suspended | banned
            $table->string('status_reason')->nullable()->after('status');
            $table->timestamp('suspended_until')->nullable()->after('status_reason');
        });

        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'status_reason', 'suspended_until']);
        });
    }
};
