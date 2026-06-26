<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index sur les colonnes de tri/filtre les plus sollicitées
     * (latest('published_at'), online()/last_seen_at, status).
     */
    public function up(): void
    {
        $this->safeIndex('posts', 'published_at');
        $this->safeIndex('demandes', 'published_at');
        $this->safeIndex('demandes', 'status');
        $this->safeIndex('users', 'last_seen_at');
        $this->safeIndex('messages', 'read_at');
    }

    public function down(): void
    {
        $this->safeDropIndex('posts', 'published_at');
        $this->safeDropIndex('demandes', 'published_at');
        $this->safeDropIndex('demandes', 'status');
        $this->safeDropIndex('users', 'last_seen_at');
        $this->safeDropIndex('messages', 'read_at');
    }

    private function safeIndex(string $table, string $column): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }
        try {
            Schema::table($table, fn (Blueprint $t) => $t->index($column));
        } catch (\Throwable $e) {
            // index déjà présent — on ignore
        }
    }

    private function safeDropIndex(string $table, string $column): void
    {
        try {
            Schema::table($table, fn (Blueprint $t) => $t->dropIndex([$column]));
        } catch (\Throwable $e) {
        }
    }
};
