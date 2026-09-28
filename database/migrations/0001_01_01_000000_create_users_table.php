<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B12 — base de uma BD nova (testes `sqlite::memory:`, `migrate:fresh` em produção).
 *
 * Na BD do dump estes INSERTs nunca correm: `create_users_table` está registado
 * manualmente na tabela `migrations` porque o `users` já importava (laravel.md §6.7).
 * Aqui fica a forma canónica (UUID + role) para quem arranca de raiz.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->string('id', 36)->primary();
                $table->string('name', 150);
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('role', ['USER', 'ADMIN'])->default('USER')->index();
                $table->string('phone', 30)->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });
        }

        // Coluna `token` (a que o DatabaseTokenRepository do Laravel lê e escreve).
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('user_id', 36)->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
