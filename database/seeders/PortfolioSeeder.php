<?php

namespace Database\Seeders;

use App\Models\PortfolioGallery;
use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

/**
 * B4 — os 6 projetos de `DEFAULT_PORTFOLIO` (ui/js/app.js), agora com `slug`
 * (para /portfolio/{slug}) e `status = published`.
 * Conteúdo é placeholder: o portefólio real vem do cliente (plan.md §12).
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        PortfolioGallery::query()->delete();
        PortfolioItem::query()->delete();

        $items = [
            [
                'title' => 'Residência Moderna',
                'slug' => 'residencia-moderna',
                'category' => 'Residencial',
                'description' => 'Projeto completo de uma residência de 450m² com design contemporâneo e integração com a natureza.',
                'area' => '450m²',
                'year' => 2024,
                'location' => 'Luanda, Angola',
                'image_url' => '/assets/portifolio/image1.png',
                'gallery' => ['/assets/portifolio/image1.png', '/assets/portifolio/image2.png', '/assets/portifolio/image3.png'],
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Escritório Corporativo',
                'slug' => 'escritorio-corporativo',
                'category' => 'Comercial',
                'description' => 'Ambiente de trabalho colaborativo para empresa de tecnologia, priorizando conforto e produtividade.',
                'area' => '280m²',
                'year' => 2024,
                'location' => 'Talatona, Luanda',
                'image_url' => '/assets/portifolio/image2.png',
                'gallery' => ['/assets/portifolio/image2.png', '/assets/portifolio/image4.png'],
                'featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Apartamento Luxo',
                'slug' => 'apartamento-luxo',
                'category' => 'Residencial',
                'description' => 'Reforma completa de apartamento de alto padrão com foco em elegância e funcionalidade.',
                'area' => '180m²',
                'year' => 2023,
                'location' => 'Miramar, Luanda',
                'image_url' => '/assets/portifolio/image3.png',
                'gallery' => ['/assets/portifolio/image3.png', '/assets/portifolio/image5.png'],
                'featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Clínica Médica',
                'slug' => 'clinica-medica',
                'category' => 'Comercial',
                'description' => 'Projeto de clínica médica humanizada, transmitindo confiança e acolhimento aos pacientes.',
                'area' => '320m²',
                'year' => 2023,
                'location' => 'Benfica, Luanda',
                'image_url' => '/assets/portifolio/image4.png',
                'gallery' => ['/assets/portifolio/image4.png', '/assets/portifolio/image6.png'],
                'featured' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Villa Sol Nascente',
                'slug' => 'villa-sol-nascente',
                'category' => 'Corporativo',
                'description' => 'Complexo corporativo e de reuniões com paisagismo integrado e acústica de alto nível.',
                'area' => '600m²',
                'year' => 2023,
                'location' => 'Luanda, Angola',
                'image_url' => '/assets/portifolio/image5.png',
                'gallery' => ['/assets/portifolio/image5.png', '/assets/portifolio/image7.png'],
                'featured' => false,
                'sort_order' => 5,
            ],
            [
                'title' => 'Showroom Havre',
                'slug' => 'showroom-havre',
                'category' => 'Outro',
                'description' => 'Espaço conceitual projetado para exibição de mobiliário exclusivo e recepção de clientes.',
                'area' => '150m²',
                'year' => 2024,
                'location' => 'Luanda, Angola',
                'image_url' => '/assets/portifolio/image6.png',
                'gallery' => ['/assets/portifolio/image6.png', '/assets/portifolio/image8.png'],
                'featured' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($items as $data) {
            $images = $data['gallery'];
            unset($data['gallery']);

            $item = PortfolioItem::create($data + ['status' => 'published']);

            foreach ($images as $i => $image) {
                $item->gallery()->create(['image_url' => $image, 'sort_order' => $i + 1]);
            }
        }

        $this->command?->info('PortfolioSeeder: ' . PortfolioItem::count() . ' projetos, '
            . PortfolioGallery::count() . ' imagens de galeria.');
    }
}
