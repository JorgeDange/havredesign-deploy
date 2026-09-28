<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\SettingController;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * /networking — página permanente do QR Code institucional.
 *
 * A URL nunca muda nem redireciona (o QR impresso tem de continuar a funcionar),
 * a imagem é substituível pelo painel de definições e o link existe no rodapé.
 */
class NetworkingTest extends TestCase
{
    use RefreshDatabase;

    /** Caminho absoluto do QR em public/ (sempre o mesmo nome). */
    private string $qrCaminho;

    /** Conteúdo de um QR que já existisse antes do teste, para repor no fim. */
    private ?string $qrOriginal = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->qrCaminho = public_path(SettingController::QR_CAMINHO);

        // Nunca apagar de verdade o QR do cliente: guarda e repõe no tearDown.
        if (is_file($this->qrCaminho)) {
            $this->qrOriginal = (string) file_get_contents($this->qrCaminho);
            unlink($this->qrCaminho);
        }
    }

    protected function tearDown(): void
    {
        if (is_file($this->qrCaminho)) {
            unlink($this->qrCaminho);
        }

        if ($this->qrOriginal !== null) {
            file_put_contents($this->qrCaminho, $this->qrOriginal);
        }

        parent::tearDown();
    }

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

    /** Payload completo do POST /definicoes (agenda.horarios é obrigatório). */
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
                'hours_lines' => 'Segunda a Sexta — 09h às 18h',
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

    public function test_networking_page_returns_200(): void
    {
        $this->get('/networking')
            ->assertStatus(200)
            ->assertSee('Networking')
            ->assertSee('Partilhe esta página.');
    }

    public function test_networking_page_never_redirects(): void
    {
        $response = $this->get('/networking');

        $response->assertStatus(200);
        $response->assertHeaderMissing('Location');
    }

    public function test_networking_page_has_the_title_and_description(): void
    {
        $html = (string) $this->get('/networking')->assertStatus(200)->getContent();

        $this->assertStringContainsString('<title>Networking — HAVREDESIGN', $html);
        $this->assertStringContainsString('name="description"', $html);
    }

    public function test_networking_is_linked_from_the_menu_and_the_footer(): void
    {
        $html = (string) $this->get('/')->assertStatus(200)->getContent();

        $href = 'href="'.route('networking').'"';

        $this->assertStringContainsString($href, $html);
        $this->assertStringContainsString('>Networking</a>', $html);

        // Uma aparição no menu de topo e outra no rodapé.
        $this->assertGreaterThanOrEqual(2, substr_count($html, $href));
    }

    public function test_page_shows_a_notice_while_the_qr_file_is_missing(): void
    {
        $this->assertFileDoesNotExist($this->qrCaminho);

        $html = (string) $this->get('/networking')->assertStatus(200)->getContent();

        $this->assertStringContainsString('O QR Code será publicado em breve', $html);
        $this->assertStringNotContainsString('alt="QR Code para a página Networking', $html);
    }

    public function test_admin_can_upload_the_qr_and_the_page_renders_it(): void
    {
        $imagem = UploadedFile::fake()->image('qrcode-networking.png', 200, 200);

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $this->payload() + ['qr_image' => $imagem])
            ->assertSessionHasNoErrors()
            ->assertRedirect($this->admin('definicoes'));

        $this->assertFileExists($this->qrCaminho);
        $this->assertSame('images/qrcode-networking.png', Setting::get('networking.qr_image'));

        $html = (string) $this->get('/networking')->assertStatus(200)->getContent();

        $this->assertStringContainsString('src="'.asset('images/qrcode-networking.png').'"', $html);
        $this->assertStringContainsString('alt="QR Code para a página Networking da HAVREDESIGN"', $html);
        $this->assertStringNotContainsString('O QR Code será publicado em breve', $html);
    }

    public function test_admin_settings_page_offers_the_qr_upload(): void
    {
        $html = (string) $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('definicoes'))
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('enctype="multipart/form-data"', $html);
        $this->assertStringContainsString('name="qr_image"', $html);
        $this->assertStringContainsString('>Networking</h2>', $html);
    }

    public function test_admin_rejects_a_file_that_is_not_an_image(): void
    {
        $ficheiro = UploadedFile::fake()->create('notas.txt', 20, 'text/plain');

        $this->actingAs($this->utilizadorAdmin())
            ->post($this->admin('definicoes'), $this->payload() + ['qr_image' => $ficheiro])
            ->assertSessionHasErrors('qr_image');

        $this->assertFileDoesNotExist($this->qrCaminho);
    }
}
