<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * B12 — a coluna `password_reset_tokens.reset_token` não existe no Laravel.
 *
 * O `DatabaseTokenRepository` lê e escreve `token`; com `reset_token` a reposição
 * de palavra-passe falhava com «Column not found» em produção. Corrige a BD do
 * dump, onde a reconcile (versão antiga) tinha criado a coluna com o nome errado.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens')
            && Schema::hasColumn('password_reset_tokens', 'reset_token')
            && ! Schema::hasColumn('password_reset_tokens', 'token')) {
            Schema::table('password_reset_tokens', function ($t) {
                $t->renameColumn('reset_token', 'token');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('password_reset_tokens')
            && Schema::hasColumn('password_reset_tokens', 'token')
            && ! Schema::hasColumn('password_reset_tokens', 'reset_token')) {
            Schema::table('password_reset_tokens', function ($t) {
                $t->renameColumn('token', 'reset_token');
            });
        }
    }
};
