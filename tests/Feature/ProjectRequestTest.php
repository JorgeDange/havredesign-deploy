<?php

namespace Tests\Feature;

use App\Models\ProjectRequest;
use App\Models\Service;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.1 — POST /solicitar-projeto (backend.md 6.1 / laravel.md B7).
 */
class ProjectRequestTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return array_merge([
            'user_name' => 'João Cliente',
            'user_email' => 'joao@example.com',
            'user_phone' => '+244 923 000 111',
            'location' => 'Luanda, Talatona',
            'project_type' => 'Apartamento T3',
            'preferred_channel' => 'email',
            'description' => 'Reabilitação completa de um apartamento T3.',
            'privacy' => '1',
        ], $extra);
    }

    public function test_project_request_page_returns_ok(): void
    {
        $this->get('/solicitar-projeto')->assertStatus(200);
    }

    public function test_valid_request_is_persisted_with_privacy_consent(): void
    {
        $response = $this->post('/solicitar-projeto', $this->payload());

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success')
            ->assertSessionHas('project_submitted')
            ->assertRedirect(route('solicitar-projeto'));

        $pedido = ProjectRequest::sole();

        $this->assertSame('NEW', $pedido->status);
        $this->assertSame('joao@example.com', $pedido->user_email);
        $this->assertNotNull($pedido->privacy_consented_at);
        $this->assertNotNull($pedido->privacy_version);
        $this->assertNotNull($pedido->ip_address);
    }

    public function test_request_may_reference_an_existing_service_and_solution(): void
    {
        $servico = Service::create([
            'title' => 'Arquitetura Interior',
            'slug' => 'arquitetura-interior',
            'description' => 'Projeto de interiores completo.',
            'group' => 'principal',
            'icon' => 'home',
        ]);

        $solucao = Solution::create([
            'code' => 'DESIGN',
            'name' => 'Design de Interiores',
            'slug' => 'design-de-interiores',
            'description' => 'Solução de design de interiores.',
        ]);

        $this->post('/solicitar-projeto', $this->payload([
            'service_id' => $servico->id,
            'solution_id' => $solucao->id,
        ]))->assertSessionHasNoErrors();

        $pedido = ProjectRequest::sole();
        $this->assertSame($servico->id, $pedido->service_id);
        $this->assertSame($solucao->id, $pedido->solution_id);
    }

    public function test_privacy_consent_is_mandatory(): void
    {
        $response = $this->post('/solicitar-projeto', $this->payload(['privacy' => null]));

        $response->assertSessionHasErrors('privacy');
        $this->assertDatabaseCount('project_requests', 0);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->post('/solicitar-projeto', [
            'user_name' => '',
            'user_email' => 'not-an-email',
            'user_phone' => '',
            'project_type' => '',
            'preferred_channel' => 'smoke-signal',
            'description' => '',
        ]);

        $response->assertSessionHasErrors([
            'user_name', 'user_email', 'user_phone', 'project_type',
            'preferred_channel', 'description',
        ]);
    }

    public function test_honeypot_field_rejects_bots(): void
    {
        $response = $this->post('/solicitar-projeto', $this->payload(['homepage' => 'https://spam.example']));

        $response->assertSessionHasErrors('homepage');
        $this->assertDatabaseCount('project_requests', 0);
    }

    public function test_logged_in_user_is_linked_to_their_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/solicitar-projeto', $this->payload())
            ->assertSessionHasNoErrors();

        $this->assertSame($user->id, ProjectRequest::sole()->user_id);
    }

    public function test_attachments_are_stored_on_the_private_disk(): void
    {
        $ficheiro = \Illuminate\Http\UploadedFile::fake()->create('planta.pdf', 120, 'application/pdf');

        $this->post('/solicitar-projeto', $this->payload([
            'projectFiles' => [$ficheiro],
        ]))->assertSessionHasNoErrors();

        $anexo = \App\Models\Attachment::sole();
        $this->assertStringStartsWith('requests/', $anexo->path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($anexo->path));
        $this->assertSame('planta.pdf', $anexo->original_name);
    }
}
