<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * B12.1 — todas as páginas públicas do site têm de responder 200
 * (laravel.md §5, mapeamento de URIs do B5/B6).
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<int, array<int, string>>
     */
    public static function paginasPublicas(): array
    {
        return [
            ['/'],
            ['/sobre'],
            ['/servicos'],
            ['/orcamentos'],
            ['/portfolio'],
            ['/processo'],
            ['/faq'],
            ['/contacto'],
            ['/solicitar-projeto'],
            ['/agendar'],
            ['/politica-de-privacidade'],
            ['/termos-de-uso'],
            ['/entrar'],
            ['/registar'],
            ['/recuperar-palavra-passe'],
        ];
    }

    #[DataProvider('paginasPublicas')]
    public function test_public_page_returns_ok(string $uri): void
    {
        $this->get($uri)->assertStatus(200);
    }

    public function test_portfolio_detail_page_returns_ok_for_published_slug(): void
    {
        $item = PortfolioItem::create([
            'title' => 'Apartamento Talatona',
            'slug' => 'apartamento-talatona',
            'category' => 'Residencial',
            'status' => 'published',
            'description' => 'Reabilitação total de um apartamento em Talatona.',
            'year' => 2024,
        ]);

        $this->get('/portfolio/'.$item->slug)->assertStatus(200);
    }

    public function test_portfolio_detail_page_404_for_unknown_slug(): void
    {
        $this->get('/portfolio/nao-existe')->assertStatus(404);
    }

    public function test_portfolio_detail_page_404_for_draft_slug(): void
    {
        PortfolioItem::create([
            'title' => 'Rascunho',
            'slug' => 'rascunho-secreto',
            'category' => 'Comercial',
            'status' => 'draft',
            'description' => 'Ainda não publicado.',
        ]);

        $this->get('/portfolio/rascunho-secreto')->assertStatus(404);
    }
}
