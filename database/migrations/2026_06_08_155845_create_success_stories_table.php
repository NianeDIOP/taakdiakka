<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('success_stories', function (Blueprint $table) {
            $table->id();
            $table->string('couple');          // « Awa & Modou »
            $table->string('initials', 4);      // monogramme, ex. « AM »
            $table->string('location');         // « Dakar · Sénégal »
            $table->string('badge_label');      // « Mariés en 2025 » / « Fiancés »
            $table->string('badge_icon')->default('rings'); // rings | heart
            $table->boolean('badge_heart')->default(false);  // cœur rouge sur le badge ?
            $table->text('quote');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('success_stories');
    }
};
