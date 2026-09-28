<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * B4 — fonte única de verdade (backend.md §4.10): rodapé, contacto, redes,
 * WhatsApp e agenda (hoje em AGENDA_CONFIG em ui/js/app.js).
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // contact — rodapé e página de contacto
            ['contact.email', 'info@havredesign.ao', 'contact'],
            ['contact.phone_1', '+244 926 184 104', 'contact'],
            ['contact.phone_2', '+244 926 334 650', 'contact'],
            ['contact.phone_3', '+244 939 718 811', 'contact'],
            ['contact.whatsapp', '+244 926 184 104', 'contact'],
            ['contact.address_full', 'Benfica, Via Expressa, Bairro Tchinguari, Rua 1, próximo à Administração do Talatona, Município de Talatona, Luanda.', 'contact'],
            ['contact.hours', 'Segunda a sexta — 09h às 18h · Sábado — 09h às 13h', 'contact'],
            ['contact.hours_lines', "Segunda a Sexta — 09h às 18h\nSábado — 09h às 13h", 'contact'],

            // brand
            ['brand.name', 'HAVREDESIGN', 'brand'],
            ['brand.legal_name', 'HAVREDESIGN — Arquitetura e Construção, Lda.', 'brand'],
            ['brand.tagline', 'Arquitetura como Refúgio, excelência em cada detalhe.', 'brand'],

            // social
            ['social.instagram', 'https://www.instagram.com/havredesign.ao/', 'social'],
            ['social.facebook', 'https://www.facebook.com/profile.php?id=61594010232462', 'social'],
            ['social.linkedin', 'https://www.linkedin.com/company/havredesign/', 'social'],

            // whatsapp
            ['whatsapp.number', '244926184104', 'whatsapp'],
            ['whatsapp.open_message', 'Olá, HAVREDESIGN! Visitou o vosso site e gostaria de receber orientação sobre o serviço ou a HAVRE Solução mais adequada para o meu projeto.', 'whatsapp'],

            // process — case study "Casa Vila Nova" (Definições → Caso Real)
            ['process.case_local', 'Benfica, Via Expressa, Bairro Tchinguari, Rua 1, Talatona, Luanda', 'process'],

            // seed version (equivalente a SERVICES_SEED_VERSION)
            ['seed.services_version', '2026-09-25-servicos-inclui-1', 'seed'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            Setting::set($key, $value, $group);
        }

        // agenda — objeto AGENDA_CONFIG de ui/js/app.js (laravel.md B4)
        // Só SITE e ONLINE: o tipo OFFICE foi removido (remoção da promessa de escritório).
        Setting::set('agenda', [
            'horarios' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'],
            'dias_indisponiveis' => [0, 6],
            'horarios_sabado' => [],
            'tipo_predefinido' => 'ONLINE',
            'tipos' => [
                'SITE' => [
                    'label' => 'Visita ao Local',
                    'precisa_endereco' => true,
                    'nota' => 'A visita ao local pode implicar uma taxa de deslocação, calculada conforme a localização e confirmada previamente.',
                ],
                'ONLINE' => [
                    'label' => 'Reunião Online',
                    'precisa_endereco' => false,
                    'nota' => 'Conversa por videochamada ou telefone, no horário combinado.',
                ],
            ],
        ], 'agenda');

        $this->command?->info('SettingsSeeder: ' . Setting::count() . ' definições.');
    }
}
