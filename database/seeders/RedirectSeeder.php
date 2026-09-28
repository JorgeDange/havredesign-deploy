<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

/**
 * B12.4 — mapa de URLs antigas → novas (laravel.md §B12, plan.md §C).
 *
 * Duas origens:
 *  (a) o site estático `ui/` (ficheiros `*.html`) — as que os visitantes têm
 *      guardadas em favoritos e os motores têm indexadas;
 *  (b) hipóteses do plan.md §C de URLs de uma versão anterior — confirmar com
 *      o sitemap e analytics reais antes de as publicar.
 *
 * Idempotente: `updateOrCreate` por `source_path`, corre bem em BD já semeada.
 */
class RedirectSeeder extends Seeder
{
    /**
     * @return array<string, string>
     */
    private const MAPA = [
        // (a) site estático ui/*.html
        '/index.html' => '/',
        '/sobre.html' => '/sobre',
        '/servicos.html' => '/servicos',
        '/orcamento.html' => '/orcamentos',
        '/portfolio.html' => '/portfolio',
        '/portfolio-detalhe.html' => '/portfolio',
        '/processo.html' => '/processo',
        '/faq.html' => '/faq',
        '/contacto.html' => '/contacto',
        '/solicitar-projeto.html' => '/solicitar-projeto',
        '/agendar.html' => '/agendar',
        '/politica-privacidade.html' => '/politica-de-privacidade',
        '/termos-uso.html' => '/termos-de-uso',
        '/login.html' => '/entrar',
        // O painel não recebe redirect: um 301 com `Location: /<ADMIN_PATH>`
        // exporia o caminho a qualquer visitante, o que anularia o obscurecimento.
        // Quem tenha o marcador volta à home e entra pelo caminho configurado.
        '/admin.html' => '/',
        '/dashboard.html' => '/conta',

        // (b) hipóteses do plan.md §C (serviços retirados, páginas renomeadas)
        '/servicos/construcao' => '/servicos',
        '/servicos/arquitetura-construcao' => '/servicos',
        '/servicos/gestao-de-obras' => '/servicos',
        '/servicos/urbanismo' => '/servicos',
        '/servicos/estudo-de-viabilidade' => '/servicos',
        '/servicos/mobiliario' => '/servicos',
        '/servicos/visualizacao-3d' => '/servicos',
        '/servicos/tour-virtual' => '/servicos',
        '/tour-virtual' => '/servicos',
        '/orcamento' => '/orcamentos',
        '/orcamento/pacotes' => '/orcamentos',
        '/orcamento/formulario' => '/solicitar-projeto',
        '/havre-solucoes' => '/orcamentos',
        '/agendar-conversa' => '/agendar',
        '/formulario' => '/solicitar-projeto',
        '/services' => '/servicos',
        '/about' => '/sobre',
        '/budget' => '/orcamentos',
        '/contact' => '/contacto',
        '/process' => '/processo',
        '/portfolio-item' => '/portfolio',
    ];

    public function run(): void
    {
        foreach (self::MAPA as $origem => $destino) {
            Redirect::updateOrCreate(
                ['source_path' => $origem],
                ['target_path' => $destino, 'status_code' => 301, 'active' => true]
            );
        }
    }
}
