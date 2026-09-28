<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\Http\Response;

/**
 * B12.4 — `robots.txt` e `sitemap.xml` gerados da configuração e da BD
 * (laravel.md §B12; backend.md §9).
 *
 * Ambos são rotas e não ficheiros estáticos: o domínio canónico vem de
 * `config('app.url')` (o `.env.production.example` fixa-o em produção) e o
 * sitemap segue o portefólio publicado sem cópias manuais.
 */
class SeoController extends Controller
{
    /**
     * Páginas públicas estáticas (routes/web.php) e o seu peso de indexação.
     *
     * @var array<string, array{priority: string, changefreq: string}>
     */
    private const PAGINAS = [
        '/' => ['priority' => '1.0', 'changefreq' => 'weekly'],
        '/sobre' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/servicos' => ['priority' => '0.9', 'changefreq' => 'monthly'],
        '/orcamentos' => ['priority' => '0.9', 'changefreq' => 'monthly'],
        '/portfolio' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        '/processo' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/faq' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/contacto' => ['priority' => '0.8', 'changefreq' => 'yearly'],
        '/solicitar-projeto' => ['priority' => '0.9', 'changefreq' => 'yearly'],
        '/agendar' => ['priority' => '0.8', 'changefreq' => 'yearly'],
        '/politica-de-privacidade' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/termos-de-uso' => ['priority' => '0.3', 'changefreq' => 'yearly'],
        '/networking' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    ];

    /**
     * Áreas privadas — nunca devem ser indexadas (autenticação e backoffice).
     *
     * O painel não está aqui em constante: o seu caminho vem de
     * `config('admin.path')` (ADMIN_PATH no .env) e é somado em `robots()`.
     *
     * @var array<int, string>
     */
    private const PROTEGIDAS = [
        '/conta', '/entrar', '/registar',
        '/recuperar-palavra-passe', '/redefinir-palavra-passe',
        '/confirmar-palavra-passe', '/verificar-email', '/up',
    ];

    public function robots(): Response
    {
        $base = $this->base();

        $linhas = ['User-agent: *', 'Allow: /'];

        foreach ($this->protegidas() as $path) {
            $linhas[] = 'Disallow: '.$path;
        }

        $linhas[] = '';
        $linhas[] = 'Sitemap: '.$base.'/sitemap.xml';

        return response(implode("\n", $linhas)."\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Caminhos a não indexar, com o painel no topo (caminho configurável).
     *
     * @return array<int, string>
     */
    private function protegidas(): array
    {
        return array_merge(['/'.config('admin.path')], self::PROTEGIDAS);
    }

    public function sitemap(): Response
    {
        $base = $this->base();
        $hoje = now()->toDateString();
        $urls = [];

        foreach (self::PAGINAS as $path => $meta) {
            $urls[] = [
                'loc' => $base.$path,
                'lastmod' => $hoje,
                'changefreq' => $meta['changefreq'],
                'priority' => $meta['priority'],
            ];
        }

        // Só portefólio publicado: rascunhos ficam fora do sitemap (não do site).
        foreach (PortfolioItem::published()->orderBy('slug')->get() as $projeto) {
            $urls[] = [
                'loc' => $base.'/portfolio/'.$projeto->slug,
                'lastmod' => $projeto->updated_at?->toDateString() ?? $hoje,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        $linhas = ['<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $linhas[] = '  <url>';
            foreach ($url as $tag => $valor) {
                $linhas[] = '    <'.$tag.'>'.$this->esc($valor).'</'.$tag.'>';
            }
            $linhas[] = '  </url>';
        }

        $linhas[] = '</urlset>';

        return response(implode("\n", $linhas)."\n", 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function base(): string
    {
        return rtrim(config('app.url'), '/');
    }

    private function esc(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
