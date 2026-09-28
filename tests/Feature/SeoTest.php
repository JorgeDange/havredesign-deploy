<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\Redirect;
use Database\Seeders\RedirectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * B12.4 — SEO (laravel.md §B12): robots.txt, sitemap.xml e 301 via `redirects`.
 */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function base(): string
    {
        return rtrim(config('app.url'), '/');
    }

    public function test_robots_txt_is_served_from_the_application(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/plain', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('User-agent: *', $response->getContent());
        $this->assertStringContainsString('Sitemap: '.$this->base().'/sitemap.xml', $response->getContent());
    }

    public function test_robots_txt_blocks_private_areas(): void
    {
        $conteudo = $this->get('/robots.txt')->getContent();

        foreach (['/'.config('admin.path'), '/conta', '/entrar', '/registar'] as $protegida) {
            $this->assertStringContainsString('Disallow: '.$protegida, $conteudo);
        }

        $this->assertStringContainsString('Allow: /', $conteudo);
    }

    public function test_sitemap_xml_is_served_and_lists_the_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringStartsWith('application/xml', (string) $response->headers->get('Content-Type'));

        $xml = $response->getContent();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $xml);

        foreach (['/', '/sobre', '/servicos', '/orcamentos', '/portfolio', '/processo', '/faq',
            '/contacto', '/solicitar-projeto', '/agendar', '/politica-de-privacidade',
            '/termos-de-uso', '/networking'] as $pagina) {
            $this->assertStringContainsString('<loc>'.$this->base().$pagina.'</loc>', $xml, "sitemap em falta: {$pagina}");
        }

        $this->assertStringContainsString('<lastmod>'.now()->toDateString().'</lastmod>', $xml);
    }

    public function test_sitemap_lists_published_projects_but_hides_drafts(): void
    {
        PortfolioItem::create([
            'title' => 'Publicado',
            'slug' => 'projeto-publicado',
            'category' => 'Residencial',
            'status' => 'published',
            'description' => 'Visível no sitemap.',
        ]);

        PortfolioItem::create([
            'title' => 'Rascunho',
            'slug' => 'projeto-rascunho',
            'category' => 'Comercial',
            'status' => 'draft',
            'description' => 'Escondido do sitemap.',
        ]);

        $xml = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString('<loc>'.$this->base().'/portfolio/projeto-publicado</loc>', $xml);
        $this->assertStringNotContainsString('projeto-rascunho', $xml);
    }

    public function test_legacy_html_url_is_permanently_redirected(): void
    {
        Redirect::create([
            'source_path' => '/sobre.html',
            'target_path' => '/sobre',
            'status_code' => 301,
            'active' => true,
        ]);

        $response = $this->get('/sobre.html');

        $response->assertStatus(301);
        $response->assertLocation('/sobre');
    }

    public function test_legacy_urls_of_removed_pages_are_redirected(): void
    {
        Redirect::create([
            'source_path' => '/servicos/construcao',
            'target_path' => '/servicos',
            'status_code' => 301,
            'active' => true,
        ]);

        $this->get('/servicos/construcao')
            ->assertStatus(301)
            ->assertLocation('/servicos');
    }

    public function test_inactive_redirect_is_ignored(): void
    {
        Redirect::create([
            'source_path' => '/velho.html',
            'target_path' => '/sobre',
            'status_code' => 301,
            'active' => false,
        ]);

        $this->get('/velho.html')->assertStatus(404);
    }

    public function test_non_get_requests_are_never_rewritten(): void
    {
        Redirect::create([
            'source_path' => '/contacto.html',
            'target_path' => '/contacto',
            'status_code' => 301,
            'active' => true,
        ]);

        $this->post('/contacto.html', [])->assertStatus(404);
    }

    public function test_unknown_urls_still_return_404(): void
    {
        $this->get('/nao-existe-nem-como-html.html')->assertStatus(404);
        $this->get('/rota-inventada')->assertStatus(404);
    }

    public function test_redirect_seeder_is_idempotent(): void
    {
        $this->seed(RedirectSeeder::class);
        $primeiro = Redirect::count();

        $this->seed(RedirectSeeder::class);

        $this->assertSame($primeiro, Redirect::count());
        $this->assertGreaterThan(0, $primeiro);
        $this->assertDatabaseHas('redirects', [
            'source_path' => '/servicos.html',
            'target_path' => '/servicos',
            'status_code' => 301,
            'active' => true,
        ]);
    }

    /**
     * plan.md §D: «301 testados um a um (sem cadeia, sem loop)».
     * O destino de cada redirecionamento nunca pode ser ele próprio uma origem.
     */
    public function test_seeded_redirects_have_no_chains_or_loops(): void
    {
        $this->seed(RedirectSeeder::class);

        $origens = Redirect::pluck('source_path')->all();
        $destinos = Redirect::pluck('target_path')->all();

        $cadeias = array_values(array_intersect($destinos, $origens));

        $this->assertSame([], $cadeias, 'Há 301 em cadeia: '.implode(', ', $cadeias));

        foreach ($origens as $origem) {
            $this->assertNotSame($origem, Redirect::where('source_path', $origem)->value('target_path'));
        }
    }

    public function test_every_seeded_target_is_a_live_route(): void
    {
        $this->seed(RedirectSeeder::class);

        foreach (Redirect::pluck('target_path') as $destino) {
            // 302 é esperado no painel e em `/conta` (áreas atrás de login);
            // o que não pode acontecer é um destino que não existe.
            $estado = $this->get($destino)->status();

            $this->assertContains($estado, [200, 302], "destino {$destino} devolveu {$estado}");
        }
    }
}
