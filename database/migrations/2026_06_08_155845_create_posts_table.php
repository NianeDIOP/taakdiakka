<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('author_name')->nullable();   // null + is_anonymous = « Anonyme »
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('author_verified')->default(false);
            $table->string('theme');                      // Confession | Témoignage | Conseil | Question
            $table->string('theme_emoji', 8)->nullable(); // 🌙 💍 💡 …
            $table->string('location')->nullable();
            $table->text('body');
            $table->unsignedInteger('hearts')->default(0);
            $table->unsignedInteger('replies')->default(0);
            $table->json('reactions')->nullable();        // ["❤️","🤲","🌹"]
            $table->json('comments')->nullable();         // [{name, verified, body, likes}]
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
