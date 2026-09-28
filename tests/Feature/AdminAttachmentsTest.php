<?php

namespace Tests\Feature;

use App\Models\Attachment;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Anexos dos pedidos de orçamento no painel admin (laravel.md §12 / backend.md 4.7).
 *
 * Os ficheiros vivem no disco privado (`storage/app/private`) e são servidos
 * só por rotas do backoffice: download sempre, visualização em separador novo
 * quando o browser sabe mostrar o tipo (PDF/imagem).
 */
class AdminAttachmentsTest extends TestCase
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
     * Cria um pedido com um anexo já gravado no disco privado (falso).
     *
     * @return array{0: ProjectRequest, 1: Attachment}
     */
    private function pedidoComAnexo(string $extensao = 'pdf', string $mime = 'application/pdf'): array
    {
        Storage::fake('local');

        $pedido = ProjectRequest::create([
            'user_name' => 'Cliente com Anexo',
            'user_email' => 'cliente@example.com',
            'user_phone' => '+244 900 000 010',
            'project_type' => 'Projeto Arquitetónico',
            'preferred_channel' => 'email',
            'description' => 'Pedido de teste com anexo.',
            'status' => 'NEW',
        ]);

        $caminho = 'requests/'.$pedido->id.'/ficheiro.'.$extensao;
        Storage::disk('local')->put($caminho, 'conteudo-do-anexo');

        $anexo = Attachment::create([
            'request_id' => $pedido->id,
            'original_name' => 'Planta da Casa.'.$extensao,
            'path' => $caminho,
            'mime_type' => $mime,
            'size_bytes' => 18,
        ]);

        return [$pedido, $anexo];
    }

    public function test_admin_can_download_an_attachment(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();

        $resposta = $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id));

        $resposta->assertStatus(200);
        $this->assertStringContainsString('attachment', (string) $resposta->headers->get('content-disposition'));
        $this->assertStringContainsString('Planta da Casa', (string) $resposta->headers->get('content-disposition'));
        $this->assertSame('conteudo-do-anexo', $resposta->streamedContent());
    }

    public function test_admin_can_preview_a_pdf_in_a_new_tab(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();

        $resposta = $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id.'/ver'));

        $resposta->assertStatus(200);
        $this->assertStringContainsString('inline', (string) $resposta->headers->get('content-disposition'));
        $this->assertSame('conteudo-do-anexo', $resposta->streamedContent());
    }

    public function test_preview_falls_back_to_download_for_files_the_browser_cannot_show(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo('dwg', 'application/autocad');

        $resposta = $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id.'/ver'));

        $resposta->assertStatus(200);
        $this->assertStringContainsString('attachment', (string) $resposta->headers->get('content-disposition'));
    }

    public function test_detail_page_offers_view_and_download_buttons(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();

        $html = (string) $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id))
            ->assertStatus(200)
            ->getContent();

        // O "Ver" abre a modal da própria página (sem separador novo).
        $this->assertStringContainsString('data-anexo-ver="'.htmlspecialchars(route('pedidos.anexo.preview', [$pedido, $anexo]), ENT_QUOTES).'"', $html);
        $this->assertStringContainsString('data-anexo-baixar="'.htmlspecialchars(route('pedidos.anexo.download', [$pedido, $anexo]), ENT_QUOTES).'"', $html);
        $this->assertStringContainsString('id="anexoModal"', $html);
        $this->assertMatchesRegularExpression('/<button[^>]*data-anexo-ver=/s', $html);
        $this->assertStringNotContainsString('sem download pelo painel', $html);
    }

    public function test_detail_page_hides_the_preview_button_for_non_viewable_files(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo('zip', 'application/zip');

        $html = (string) $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id))
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id), $html);
        $this->assertStringNotContainsString($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id.'/ver'), $html);
        $this->assertStringContainsString('Baixar', $html);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();

        $this->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id))
            ->assertStatus(302)
            ->assertRedirect(route('login'));

        $this->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id.'/ver'))
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    public function test_plain_user_is_forbidden(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id))
            ->assertStatus(403);
    }

    public function test_an_attachment_of_another_request_is_not_found(): void
    {
        [$pedido] = $this->pedidoComAnexo();
        [$outroPedido] = $this->pedidoComAnexo('png', 'image/png');

        $anexoDoOutro = Attachment::where('request_id', $outroPedido->getKey())->sole();

        $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexoDoOutro->id))
            ->assertStatus(404);
    }

    public function test_missing_file_on_disk_is_404(): void
    {
        [$pedido, $anexo] = $this->pedidoComAnexo();
        Storage::disk('local')->delete($anexo->path);

        $this->actingAs($this->utilizadorAdmin())
            ->get($this->admin('pedidos/'.$pedido->id.'/anexos/'.$anexo->id))
            ->assertStatus(404);
    }
}
