<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();         // gratuit | mensuel | annuel
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->integer('price');                 // FCFA, 0 = gratuit
            $table->integer('compare_at_price')->nullable(); // prix barré (ex. 6000)
            $table->integer('duration_days')->nullable();    // null = illimité (gratuit)
            $table->json('features')->nullable();     // liste de points affichés
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('boost_packs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->integer('price');
            $table->integer('duration_days');         // durée de mise en avant
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending | active | expired | cancelled
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('amount')->default(0);
            $table->string('payment_provider')->nullable(); // paydunya | manuel | stub
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });

        Schema::create('boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('boost_pack_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('amount')->default(0);
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boosts');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('boost_packs');
        Schema::dropIfExists('plans');
    }
};
