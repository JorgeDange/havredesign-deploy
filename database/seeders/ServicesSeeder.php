<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * B4 — os 10 serviços de `DEFAULT_SERVICES` (ui/js/app.js), com o campo `group`
 * e os 21 itens de «O que inclui» (3 serviços principais × 7).
 * Substitui os 5 serviços antigos do dump.
 */
class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\ServiceInclude::query()->delete();
        Service::query()->delete();

        $services = [
            [
                'group' => 'principal',
                'title' => 'Projeto Arquitetónico',
                'slug' => 'arquitetura',
                'description' => 'Desenvolvimento de projetos arquitetónicos para edifícios residenciais, comerciais, de serviços e outras tipologias compatíveis com a estratégia da empresa.',
                'icon' => 'home',
                'image_url' => '/assets/servicos/image3.png',
                'sort_order' => 1,
                'includes' => [
                    'Recolha de informação e definição do programa de necessidades',
                    'Estudo prévio e anteprojeto',
                    'Projeto de execução, com coordenação das especialidades',
                    'Peças descritivas, desenhos e especificações técnicas',
                    'Perspetivas e imagens de apresentação',
                    'Revisões com o cliente até à aprovação',
                    'Apoio ao processo de aprovação, conforme âmbito contratado',
                ],
            ],
            [
                'group' => 'principal',
                'title' => 'Design de Interiores',
                'slug' => 'interiores',
                'description' => 'Conceção de espaços interiores orientada para a funcionalidade, a identidade do ambiente e a valorização do investimento realizado pelo cliente.',
                'icon' => 'sparkles',
                'image_url' => '/assets/servicos/image1.png',
                'sort_order' => 2,
                'includes' => [
                    'Conceito de design, paleta de cores e materiais',
                    'Layout e distribuição funcional dos espaços',
                    'Projeto de iluminação e de acabamentos',
                    'Mobiliário à medida, quando incluído no âmbito',
                    'Seleção de equipamentos e elementos de decoração',
                    'Planta, perspetivas e imagens de apresentação',
                    'Acompanhamento da execução do interior',
                ],
            ],
            [
                'group' => 'principal',
                'title' => 'Fiscalização e Acompanhamento Técnico',
                'slug' => 'fiscalizacao',
                'description' => 'Fiscalização e acompanhamento técnico durante a execução da obra, de acordo com o âmbito contratado, incluindo a verificação da conformidade dos trabalhos com os projetos e as condições técnicas aplicáveis.',
                'icon' => 'wrench',
                'image_url' => '/assets/servicos/image5.png',
                'sort_order' => 3,
                'includes' => [
                    'Visitas periódicas à obra, conforme o âmbito contratado',
                    'Verificação da conformidade dos trabalhos com o projeto aprovado',
                    'Controlo da qualidade dos materiais e da execução',
                    'Acompanhamento de prazos e sequência de trabalhos',
                    'Registo fotográfico e relatório de fiscalização',
                    'Comunicação com empreiteiros e fornecedores',
                    'Levantamento de questões e proposta de soluções',
                ],
            ],
            [
                'group' => 'complementar',
                'title' => 'Consultoria Técnica',
                'slug' => 'consultoria-tecnica',
                'description' => 'Apoio especializado à análise de projetos, tomada de decisões e avaliação de soluções técnicas.',
                'icon' => 'lightbulb',
                'image_url' => '/assets/servicos/image4.png',
                'sort_order' => 4,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Topografia',
                'slug' => 'topografia',
                'description' => 'Levantamento planimétrico e altimétrico do terreno, quando necessário ao desenvolvimento do projeto.',
                'icon' => 'map-pin',
                'image_url' => null,
                'sort_order' => 5,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Levantamento Técnico',
                'slug' => 'levantamento-tecnico',
                'description' => 'Recolha e registo das condições existentes de edifícios e espaços para apoio ao desenvolvimento do projeto.',
                'icon' => 'maximize',
                'image_url' => null,
                'sort_order' => 6,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Modelação 3D e Renderização',
                'slug' => 'modelacao-3d',
                'description' => 'Produção de modelos tridimensionais e imagens ilustrativas para facilitar a visualização e a compreensão das propostas.',
                'icon' => 'image',
                'image_url' => null,
                'sort_order' => 7,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Medições e Orçamento',
                'slug' => 'medicoes-e-orcamento',
                'description' => 'Quantificação de trabalhos e estimativa de custos para apoio ao planeamento e à execução.',
                'icon' => 'dollar-sign',
                'image_url' => null,
                'sort_order' => 8,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Licenciamento',
                'slug' => 'licenciamento',
                'description' => 'Preparação e acompanhamento dos processos administrativos necessários à aprovação de projetos junto das entidades competentes.',
                'icon' => 'calendar',
                'image_url' => null,
                'sort_order' => 9,
                'includes' => [],
            ],
            [
                'group' => 'complementar',
                'title' => 'Croqui de Localização',
                'slug' => 'croqui-localizacao',
                'description' => 'Representação da localização do imóvel para apoio a processos documentais e administrativos.',
                'icon' => 'trees',
                'image_url' => null,
                'sort_order' => 10,
                'includes' => [],
            ],
        ];

        foreach ($services as $data) {
            $includes = $data['includes'];
            unset($data['includes']);

            $service = Service::create($data + ['active' => true]);

            foreach ($includes as $i => $item) {
                $service->includes()->create(['item' => $item, 'sort_order' => $i + 1]);
            }
        }

        $this->command?->info('ServicesSeeder: ' . Service::count() . ' serviços, '
            . Service::with('includes')->get()->sum(fn ($s) => $s->includes->count()) . ' itens de «inclui».');
    }
}
