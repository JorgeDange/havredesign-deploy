<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.1 — POST /contacto (backend.md 6.2/6.4).
 */
class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return array_merge([
            'name' => 'Maria Cliente',
            'email' => 'maria@example.com',
            'phone' => '+244 926 184 104',
            'subject' => 'Pedido de orçamento',
            'message' => 'Gostaria de saber mais sobre os vossos serviços.',
        ], $extra);
    }

    public function test_contact_form_page_returns_ok(): void
    {
        $this->get('/contacto')->assertStatus(200);
    }

    public function test_valid_message_is_persisted_and_redirects_home(): void
    {
        $response = $this->post('/contacto', $this->payload());

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success')
            ->assertRedirect(route('contacto'));

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'maria@example.com',
            'subject' => 'Pedido de orçamento',
            'status' => 'new',
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->post('/contacto', [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_honeypot_field_rejects_bots(): void
    {
        $response = $this->post('/contacto', $this->payload(['homepage' => 'https://spam.example']));

        $response->assertSessionHasErrors('homepage');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_ip_address_is_recorded(): void
    {
        $this->post('/contacto', $this->payload());

        $this->assertNotNull(ContactMessage::first()->ip_address);
    }
}
