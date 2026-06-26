<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reportable'); // post | comment
            $table->string('reason');
            $table->string('status')->default('pending'); // pending | resolved | dismissed
            $table->timestamps();
            $table->unique(['reporter_id', 'reportable_id', 'reportable_type'], 'report_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('is_admin'));
    }
};
