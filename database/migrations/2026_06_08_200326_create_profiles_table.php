<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('gender')->nullable();              // Homme | Femme
            $table->date('birthdate')->nullable();             // -> âge auto
            $table->string('religion')->nullable();            // Islam | Christianisme | Autre
            $table->string('practice')->nullable();            // Pratiquant(e) | Modéré(e) | Non pratiquant(e)
            $table->string('marital_status')->nullable();      // Célibataire | Divorcé(e) | Veuf(ve)
            $table->boolean('has_children')->nullable();
            $table->unsignedTinyInteger('children_count')->nullable();
            $table->string('wants_children')->nullable();      // Oui | Non | Plus tard
            $table->string('union_type')->nullable();          // Monogame | Polygame | Indifférent
            $table->string('education')->nullable();           // niveau d'étude
            $table->string('profession')->nullable();
            $table->json('languages')->nullable();             // [Wolof, Français, ...]
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->string('complexion')->nullable();          // teint : Clair | Caramel | Foncé
            $table->string('region')->nullable();
            $table->text('bio')->nullable();                   // présentation
            $table->string('seeking')->nullable();             // Une épouse | Un époux
            $table->string('photo')->nullable();               // fichier photo de profil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
