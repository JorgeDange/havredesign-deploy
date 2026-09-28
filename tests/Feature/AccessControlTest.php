<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.1 — isolamento da área do cliente (/conta) e controlo de acesso ao painel
 * (laravel.md B3 e B9).
 *
 * O caminho do painel vem sempre de `config('admin.path')` (ADMIN_PATH) —
 * nos testes é `backoffice` (phpunit.xml), o que prova que nada está hardcoded.
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    /** Caminho absoluto do painel, respeitando o ADMIN_PATH em vigor. */
    private function admin(string $rota = ''): string
    {
        $base = '/'.config('admin.path');

        return $rota === '' ? $base : $base.'/'.ltrim($rota, '/');
    }

    private function utilizadorAdmin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'ADMIN'])->save();

        return $user;
    }

    public function test_guest_is_redirected_to_login_from_conta(): void
    {
        $this->get('/conta')->assertRedirect('/entrar');
    }

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $this->get($this->admin())->assertRedirect('/entrar');
    }

    public function test_admin_path_is_not_the_default_one(): void
    {
        $this->assertNotSame('admin', config('admin.path'));
        $this->assertSame('backoffice', config('admin.path'));
    }

    public function test_user_can_open_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/conta')->assertStatus(200);
    }

    public function test_plain_user_is_forbidden_from_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->admin())->assertStatus(403);
        $this->actingAs($user)->get($this->admin('servicos'))->assertStatus(403);
        $this->actingAs($user)->get($this->admin('pedidos'))->assertStatus(403);
    }

    public function test_admin_can_open_the_dashboard(): void
    {
        $this->actingAs($this->utilizadorAdmin())->get($this->admin())->assertStatus(200);
    }

    public function test_admin_can_open_the_main_sections(): void
    {
        $admin = $this->utilizadorAdmin();

        foreach (['servicos', 'portfolio', 'solucoes', 'pedidos',
                  'agendamentos', 'mensagens', 'definicoes',
                  'utilizadores', 'testemunhos'] as $seccao) {
            $uri = $this->admin($seccao);

            $this->actingAs($admin)->get($uri)->assertStatus(200, "GET {$uri}");
        }
    }

    public function test_account_only_shows_the_owner_records(): void
    {
        $dono = User::factory()->create();
        $outro = User::factory()->create();

        ProjectRequest::create([
            'user_id' => $dono->id,
            'user_name' => 'Dona do Pedido',
            'user_email' => $dono->email,
            'user_phone' => '+244 900 000 001',
            'project_type' => 'Moradia DoNORO',
            'preferred_channel' => 'email',
            'description' => 'Pedido privado do dono da conta.',
            'status' => 'NEW',
        ]);

        ProjectRequest::create([
            'user_id' => $outro->id,
            'user_name' => 'Outra Pessoa',
            'user_email' => $outro->email,
            'user_phone' => '+244 900 000 002',
            'project_type' => 'Escritorio OUTRO',
            'preferred_channel' => 'email',
            'description' => 'Pedido privado de outra pessoa.',
            'status' => 'NEW',
        ]);

        $this->actingAs($dono)->get('/conta')
            ->assertStatus(200)
            ->assertSee('Moradia DoNORO')
            ->assertDontSee('Escritorio OUTRO');
    }

    public function test_account_only_shows_the_owner_appointments(): void
    {
        $dono = User::factory()->create();
        $outro = User::factory()->create();

        $data = now()->addDays(21)->toDateString();

        Appointment::create([
            'user_id' => $dono->id,
            'user_name' => $dono->name,
            'user_email' => $dono->email,
            'user_phone' => '+244 900 000 001',
            'type' => 'SITE',
            'address' => 'Visita ao Domicilio DO DONO',
            'appt_date' => $data,
            'appt_time' => '09:00',
        ]);

        Appointment::create([
            'user_id' => $outro->id,
            'user_name' => $outro->name,
            'user_email' => $outro->email,
            'user_phone' => '+244 900 000 002',
            'type' => 'SITE',
            'address' => 'Visita ao Domicilio DE OUTRA PESSOA',
            'appt_date' => $data,
            'appt_time' => '11:00',
        ]);

        $this->actingAs($dono)->get('/conta')
            ->assertStatus(200)
            ->assertSee('Visita ao Domicilio DO DONO')
            ->assertDontSee('Visita ao Domicilio DE OUTRA PESSOA');
    }
}
