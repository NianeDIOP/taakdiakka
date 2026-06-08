<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();          // null = profil discret (affiché « Membre »)
            $table->unsignedTinyInteger('age');
            $table->string('seeking')->nullable();        // « Une épouse » / « Un époux »
            $table->string('profession')->nullable();
            $table->string('region');                     // ex. « Dakar, Sénégal » / « Paris · Diaspora »
            $table->text('quote');
            $table->json('tags')->nullable();
            $table->string('photo')->nullable();          // ex. « profil-1.png »
            $table->boolean('is_discret')->default(false);
            $table->boolean('is_verified')->default(true);
            $table->string('verification_level')->default('Bronze'); // Bronze / Argent / Or
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
