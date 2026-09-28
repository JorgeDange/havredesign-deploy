<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ComponentAttributeBag;
use Tests\TestCase;
use Zoker\ResponsiveImages\ResponsiveImagesService;

/**
 * FASE 6 — imagens responsivas (<x-responsive-image />).
 *
 * Cobre o contrato do componente (picture + source WebP + srcset, lazy por
 * defeito, eager+fetchpriority acima da dobra, fallback no original png/jpeg/jpg,
 * width/height contra CLS, alt obrigatório) e a regra de que nenhuma view
 * pública volta a ter um <img> cru.
 */
class ResponsiveImagesTest extends TestCase
{
    use RefreshDatabase;

    /** Variantes mínimas de que os testes dependem (geradas uma vez). */
    protected function setUp(): void
    {
        parent::setUp();

        app(ResponsiveImagesService::class)->generate('logo.png', 400, null, 'web');
    }

    /** Conta ocorrências de um padrão no HTML. */
    private function contar(string $html, string $padrao): int
    {
        return substr_count($html, $padrao);
    }

    /** Extrai todos os blocos <picture>...</picture> do HTML. */
    private function blocosPicture(string $html): array
    {
        preg_match_all('#<picture>.*?</picture>#s', $html, $m);

        return $m[0];
    }

    /** Extrai todas as tags <img ...> do HTML. */
    private function tagsImg(string $html): array
    {
        preg_match_all('#<img\b[^>]*>#', $html, $m);

        return $m[0];
    }

    public function test_home_page_wraps_every_image_in_a_picture_with_a_webp_source(): void
    {
        $html = (string) $this->get('/')->assertStatus(200)->getContent();

        $pictures = $this->blocosPicture($html);

        // Cada imagem pública é envolvida num <picture>...
        $this->assertGreaterThanOrEqual(4, count($pictures));

        // ...com <source type="image/webp" srcset="..."> e o <img> original.
        foreach ($pictures as $bloco) {
            $this->assertStringContainsString('type="image/webp"', $bloco);
            $this->assertStringContainsString('srcset="', $bloco);
            $this->assertMatchesRegularExpression('#srcset="[^"]+\d+w#', $bloco);
            $this->assertStringContainsString('<img', $bloco);
        }

        $this->assertSame(0, $this->contar($html, '<img loading'), 'nenhum <img> pode fugir ao componente');
    }

    public function test_only_images_above_the_fold_are_eager_with_high_fetch_priority(): void
    {
        foreach (['/', '/sobre', '/processo'] as $rota) {
            $html = (string) $this->get($rota)->assertStatus(200)->getContent();

            // Loader + logótipo do cabeçalho: as únicas imagens acima da dobra.
            $this->assertSame(2, $this->contar($html, 'loading="eager"'), $rota);
            $this->assertSame(2, $this->contar($html, 'fetchpriority="high"'), $rota);

            // Todo o resto continua com lazy.
            $this->assertGreaterThanOrEqual(1, $this->contar($html, 'loading="lazy"'), $rota);
        }
    }

    public function test_every_rendered_image_has_alt_width_height_and_loading(): void
    {
        $html = (string) $this->get('/processo')->assertStatus(200)->getContent();

        $imgs = $this->tagsImg($html);
        $this->assertNotEmpty($imgs);

        foreach ($imgs as $img) {
            $this->assertMatchesRegularExpression('#\salt="[^"]*"#', $img, $img);
            $this->assertStringContainsString('width="', $img, $img);
            $this->assertStringContainsString('height="', $img, $img);
            $this->assertStringContainsString('loading="', $img, $img);
            $this->assertStringContainsString('decoding="async"', $img, $img);
        }
    }

    public function test_fallback_img_src_points_to_the_original_file_and_not_to_webp(): void
    {
        $html = (string) $this->get('/')->assertStatus(200)->getContent();

        // O <img> (fallback) continua a apontar ao original png/jpeg/jpg...
        $this->assertStringContainsString('src="'.asset('logo.png').'"', $html);
        $this->assertStringNotContainsString('src="'.asset('logo.png').'.webp', $html);

        // ...e o onerror antigo foi preservado (via prop `fallback`).
        $this->assertStringContainsString('this.onerror=null;this.src=', $html);
        $this->assertStringContainsString('LOGO-HAVREDESIGN.jpeg', $html);
    }

    public function test_portofolio_images_use_the_card_preset_and_stay_lazy(): void
    {
        $html = (string) $this->get('/processo')->assertStatus(200)->getContent();

        // preset card → variantes 400 e 800 (nunca upscale além do original).
        $this->assertStringContainsString('portifolio/image1/image1-400-', $html);
        $this->assertStringContainsString('portifolio/image1/image1-800-', $html);
        $this->assertStringContainsString('processo/image1/image1-400-', $html);
    }

    public function test_component_renders_lazy_by_default_and_eager_with_fetch_priority_on_request(): void
    {
        $lazy = (string) view('components.responsive-image', [
            'path' => 'logo.png',
            'alt' => 'Logótipo',
            'preset' => 'thumbnail',
            'attributes' => new ComponentAttributeBag(['class' => 'w-full']),
        ]);

        $this->assertStringContainsString('<picture>', $lazy);
        $this->assertStringContainsString('type="image/webp"', $lazy);
        $this->assertStringContainsString('loading="lazy"', $lazy);
        $this->assertStringNotContainsString('fetchpriority', $lazy);
        $this->assertStringContainsString('class="w-full"', $lazy);
        $this->assertStringContainsString('width="714"', $lazy);
        $this->assertStringContainsString('height="313"', $lazy);

        $eager = (string) view('components.responsive-image', [
            'path' => 'logo.png',
            'alt' => 'Logótipo',
            'preset' => 'thumbnail',
            'loading' => 'eager',
            'attributes' => new ComponentAttributeBag,
        ]);

        $this->assertStringContainsString('loading="eager"', $eager);
        $this->assertStringContainsString('fetchpriority="high"', $eager);
    }

    public function test_preset_limits_the_generated_widths_and_never_upscales(): void
    {
        $thumbnail = (string) view('components.responsive-image', [
            'path' => 'logo.png',
            'alt' => 'Logótipo',
            'preset' => 'thumbnail',
            'attributes' => new ComponentAttributeBag,
        ]);

        // thumbnail → 200 e 400; o original (714px) não é alvo nem entra no srcset.
        $this->assertStringContainsString('logo-200-', $thumbnail);
        $this->assertStringContainsString('logo-400-', $thumbnail);
        $this->assertStringNotContainsString('logo-714-', $thumbnail);

        // Conteúdo mais largo do que o preset nunca é gerado (corta no original).
        $this->assertStringContainsString('width="714"', $thumbnail);
        $this->assertStringContainsString('height="313"', $thumbnail);
    }

    public function test_missing_file_still_renders_the_original_src_for_the_onerror_fallback(): void
    {
        $html = (string) view('components.responsive-image', [
            'path' => 'assets/nao-existe.png',
            'alt' => 'Em falta',
            'fallback' => 'assets/hero-section.png',
            'attributes' => new ComponentAttributeBag,
        ]);

        $this->assertStringContainsString('src="'.asset('assets/nao-existe.png').'"', $html);
        $this->assertStringNotContainsString('<source', $html);
        $this->assertStringContainsString('this.onerror=null;this.src=', $html);
        $this->assertStringContainsString(asset('assets/hero-section.png'), $html);
    }

    public function test_public_views_have_no_raw_img_tags_left(): void
    {
        $raiz = dirname(__DIR__, 2).'/resources/views';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($raiz, \FilesystemIterator::SKIP_DOTS)
        );

        $violacoes = [];

        foreach ($iterator as $ficheiro) {
            if (! $ficheiro->isFile() || $ficheiro->getExtension() !== 'blade.php') {
                continue;
            }

            $caminho = str_replace('\\', '/', $ficheiro->getPathname());

            // O componente é o único sítio com <img>; o painel não foi migrado.
            if (str_contains($caminho, '/components/responsive-image.blade.php')) {
                continue;
            }

            if (str_contains($caminho, '/views/admin/')) {
                continue;
            }

            // QR Code de /networking: imagem estática escaneável, sem variantes
            // WebP nem srcset — tem de ser servida exactamente como foi carregada.
            if (str_contains($caminho, '/views/networking.blade.php')) {
                continue;
            }

            $conteudo = (string) file_get_contents($ficheiro->getPathname());

            if (str_contains($conteudo, '<img')) {
                $violacoes[] = str_replace('\\', '/', substr($caminho, strlen($raiz) + 1));
            }
        }

        $this->assertSame([], $violacoes, 'views públicas ainda com <img> cru');
    }
}
