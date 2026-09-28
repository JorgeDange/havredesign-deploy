<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * FASE 3 — remoção da promessa de atendimento em escritório.
 *
 * Cobre o que passou a ser editável no painel (tipos de reunião sem OFFICE e o
 * "Local" do caso real) e a eliminação de OFFICE do fluxo público de agendamento.
 */
class SiteSettingsTest extends TestCase
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

    /**
     * `$site` é partilhado no boot da aplicação — antes de as migrações correrem,
     * pelo que em testes chega cheio de nulls. Re-partilha as chaves que se vão testar.
     */
    private function partilharSite(string ...$chaves): void
    {
        View::share('site', array_merge(
            (array) (View::getShared()['site'] ?? []),
            Setting::many($chaves)
        ));
    }

    /** Payload completo do POST /definicoes (nada pode ficar por preencher). */
    private function payload(): array
    {
        return [
            'contact' => [
                'email' => 'info@havredesign.ao',
                'phone_1' => '+244 926 184 104',
                'phone_2' => '',
                'phone_3' => '',
                'whatsapp' => '+244 926 184 104',
                'address_full' => 'Morada de teste',
                'hours' => 'Segunda a sexta — 09h às 18h',
                'hours_lines' => "Segunda a Sexta — 09h às 18h\nSábado — 09h às 13h",
            ],
            'brand' => [
                'name' => 'HAVREDESIGN',
                'legal_name' => 'HAVREDESIGN — Arquitetura e Construção, Lda.',
                'tagline' => 'Arquitetura como Refúgio.',
            ],
            'social' => [
                'instagram' => 'https://www.instagram.com/havredesign.ao/',
                'facebook' => 'https://www.facebook.com/havredesign',
                'linkedin' => 'https://www.linkedin.com/company/havredesign/',
            ],
            'whatsapp' => [
                'number' => '244926184104',
                'open_message' => 'Olá, HAVREDESIGN!',
            ],
            'process' => [
                'case_local' => 'Local do caso real',
            ],
            'agenda' => [
                'horarios' => "09:00\n10:00",
                'horarios_sabado' => '',
                'dias_indisponiveis' => [0, 6],
                'tipo_predefinido' => 'ONLINE',
                'tipos' => [
                    'SITE' => ['label' => 'Visita ao Local', 'nota' => 'Nota da visita.'],
                    'ONLINE' => ['label' => 'Reunião Online', 'nota' => 'Nota online.'],
                ],
            ],
        ];
    }

    public function test_admin_page_no_longer_offers_the_office_type(): void
    {
        $html = (string) $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('definicoes'))
            ->assertStatus(200)
            ->getContent();

        $this->assertStringNotContainsString("value=\"OFFICE\"", $html);
        $this->assertStringNotContainsString('Reunião no Escritório', $html);
        $this->assertStringContainsString('Textos dos tipos de reunião', $html);
        $this->assertStringContainsString('Caso real', $html);
    }

    public function test_admin_can_save_meeting_type_labels_and_notes(): void
    {
        $dados = $this->payload();
        $dados['agenda']['tipos']['SITE']['label'] = 'Visita ao Local (editada)';
        $dados['agenda']['tipos']['ONLINE']['nota'] = 'Nota online (editada).';

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $dados)
            ->assertSessionHasNoErrors()
            ->assertRedirect($this->admin('definicoes'));

        $agenda = Setting::get('agenda');

        $this->assertSame('Visita ao Local (editada)', $agenda['tipos']['SITE']['label']);
        $this->assertSame('Nota online (editada).', $agenda['tipos']['ONLINE']['nota']);
        $this->assertSame('ONLINE', $agenda['tipo_predefinido']);
        $this->assertArrayNotHasKey('OFFICE', $agenda['tipos']);

        // precisa_endereco não é editável: só a visita ao local precisa de morada.
        $this->assertTrue($agenda['tipos']['SITE']['precisa_endereco']);
        $this->assertFalse($agenda['tipos']['ONLINE']['precisa_endereco']);
    }

    public function test_admin_can_save_the_case_local(): void
    {
        $dados = $this->payload();
        $dados['process']['case_local'] = 'Outro local do caso real';

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $dados)
            ->assertSessionHasNoErrors();

        $this->assertSame('Outro local do caso real', Setting::get('process.case_local'));
    }

    public function test_office_cannot_be_selected_as_the_default_type(): void
    {
        $dados = $this->payload();
        $dados['agenda']['tipo_predefinido'] = 'OFFICE';

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $dados)
            ->assertSessionHasErrors('agenda.tipo_predefinido');
    }

    public function test_office_cannot_be_submitted_as_a_meeting_type(): void
    {
        $dados = $this->payload();
        $dados['agenda']['tipos']['OFFICE'] = ['label' => 'Reunião no Escritório', 'nota' => ''];

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $dados)
            ->assertSessionHasErrors('agenda.tipos');
    }

    public function test_case_local_is_rendered_on_the_process_page(): void
    {
        Setting::set('process.case_local', 'Local de teste do caso real', 'process');
        $this->partilharSite('process.case_local');

        $this->get('/processo')
            ->assertStatus(200)
            ->assertSee('Local de teste do caso real');
    }

    public function test_case_local_is_hidden_when_the_setting_is_empty(): void
    {
        Setting::set('process.case_local', '', 'process');
        $this->partilharSite('process.case_local');

        $this->get('/processo')
            ->assertStatus(200)
            ->assertDontSee('>Local</p>');
    }
}
