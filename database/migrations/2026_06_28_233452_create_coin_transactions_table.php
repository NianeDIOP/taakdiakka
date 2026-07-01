<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // purchase, spend_gift, spend_super, refund
            $table->integer('coins'); // positif = crédit, négatif = débit
            $table->unsignedInteger('balance_after');
            $table->string('description', 255)->nullable();
            $table->nullableMorphs('reference'); // coin_pack, gift, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
