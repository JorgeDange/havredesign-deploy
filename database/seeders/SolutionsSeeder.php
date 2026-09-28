<?php

namespace Database\Seeders;

use App\Models\Solution;
use Illuminate\Database\Seeder;

/**
 * B4 — as 5 HAVRE Soluções de ui/orcamento.html.
 * `description` guarda o texto do cartão: parágrafo + lista «✓» (separados por
 * linha em branco) — o separador é \n\n, útil para a view em B6.
 * Sem preços (briefing proíbe; honorários em proposta comercial).
 */
class SolutionsSeeder extends Seeder
{
    public function run(): void
    {
        Solution::query()->delete();

        $solutions = [
            [
                'code' => 'GENESIS',
                'name' => 'HAVRE Genesis — Arquitetura de Raiz',
                'slug' => 'havre-genesis',
                'description' => "Solução destinada ao desenvolvimento de projetos arquitetónicos de raiz, podendo incluir diagnóstico,\ndefinição do programa, desenvolvimento do projeto e acompanhamento técnico, de acordo com o âmbito contratado.\n\n✓ Diagnóstico\n✓ Definição do programa\n✓ Desenvolvimento do projeto\n✓ Acompanhamento técnico\nConforme o âmbito contratado.",
                'sort_order' => 1,
            ],
            [
                'code' => 'EVOLUTION',
                'name' => 'HAVRE Evolution — Transformação de Espaços',
                'slug' => 'havre-evolution',
                'description' => "Solução destinada à transformação, adaptação e valorização de espaços existentes, através de arquitetura,\ndesign de interiores e serviços técnicos selecionados.\n\n✓ Arquitetura\n✓ Design de interiores\n✓ Serviços técnicos selecionados",
                'sort_order' => 2,
            ],
            [
                'code' => 'READY',
                'name' => 'HAVRE Ready — Regularização e Licenciamento',
                'slug' => 'havre-ready',
                'description' => "Solução destinada à preparação, organização, submissão e acompanhamento de processos de regularização\ne licenciamento junto das entidades competentes.\n\n✓ Preparação do processo\n✓ Organização documental\n✓ Submissão\n✓ Acompanhamento junto das entidades competentes",
                'sort_order' => 3,
            ],
            [
                'code' => 'GUARDIAN',
                'name' => 'HAVRE Guardian — Fiscalização e Acompanhamento Técnico',
                'slug' => 'havre-guardian',
                'description' => "Solução destinada ao apoio técnico durante a execução da obra, através de fiscalização, consultoria e\noutras modalidades de acompanhamento, de acordo com o âmbito contratado.\n\n✓ Fiscalização\n✓ Consultoria\n✓ Outras modalidades de acompanhamento\nConforme o âmbito contratado.",
                'sort_order' => 4,
            ],
            [
                'code' => 'PRIME',
                'name' => 'HAVRE Prime — Solução Exclusiva',
                'slug' => 'havre-prime',
                'description' => "Solução personalizada para projetos que exigem a articulação de vários serviços e um nível mais elevado\nde coordenação, com âmbito definido de acordo com as características de cada contratação.\n\n✓ Articulação de vários serviços\n✓ Coordenação de âmbito elevado\n✓ Âmbito definido por contratação",
                'sort_order' => 5,
            ],
        ];

        foreach ($solutions as $data) {
            Solution::create($data + [
                'cta_label' => 'Solicitar proposta personalizada',
                'active' => true,
            ]);
        }

        $this->command?->info('SolutionsSeeder: ' . Solution::count() . ' soluções.');
    }
}
