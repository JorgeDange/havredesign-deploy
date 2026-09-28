<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * B4 — substitui o hash placeholder do dump por um hash real (laravel.md §6.8).
 * A password vem de ADMIN_PASSWORD no .env (fora do Git); se não existir,
 * gera-se uma aleatória e mostra-a UMA vez na consola.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@havredesign.com';
        $password = env('ADMIN_PASSWORD');
        $generated = false;

        if (! $password) {
            $password = Str::random(16);
            $generated = true;
        }

        $user = User::query()->updateOrCreate(['email' => $email], []);

        $user->forceFill([
            'name' => 'Administrador',
            'password' => $password, // cast 'hashed'
            'role' => 'ADMIN',       // fora do $fillable — forceFill
            'email_verified_at' => now(),
        ])->save();

        if ($generated) {
            $this->command?->warn("ADMIN_PASSWORD não está no .env — password gerada: {$password}");
            $this->command?->warn('Guarde-a e acrescente ADMIN_PASSWORD=... ao .env antes de publicar.');
        } else {
            $this->command?->info("AdminUserSeeder: admin atualizado (password vinda do .env).");
        }
    }
}
