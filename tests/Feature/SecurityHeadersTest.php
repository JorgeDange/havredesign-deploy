<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.3 — segurança (laravel.md §B12):
 * cabeçalhos, APP_DEBUG, throttle do login e cookies.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_sent_on_public_pages(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '0');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_security_headers_are_sent_on_error_responses(): void
    {
        $this->get('/rota-que-nao-existe')
            ->assertStatus(404)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_security_headers_are_sent_on_json_responses(): void
    {
        $this->getJson('/agendar/disponibilidade?mes=2026-10')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_hsts_is_sent_only_in_production(): void
    {
        config(['app.env' => 'production']);

        $this->get('/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_production_template_disables_debug_and_requires_secure_cookies(): void
    {
        $template = file_get_contents(base_path('.env.production.example'));

        $this->assertStringContainsString('APP_ENV=production', $template);
        $this->assertStringContainsString('APP_DEBUG=false', $template);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $template);
        $this->assertStringContainsString('LOG_LEVEL=warning', $template);
    }

    public function test_session_cookie_is_configured_for_browser_safety(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));
        // Em produção o template .env.production.example traz SESSION_SECURE_COOKIE=true.
        $this->assertNull(config('session.secure'));
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $user = \App\Models\User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/entrar', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post('/entrar', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }
}
