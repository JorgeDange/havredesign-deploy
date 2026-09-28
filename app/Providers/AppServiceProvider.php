<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\AgendaService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Definições partilhadas por todas as views (rodapé, header, WhatsApp, agenda). */
    private const SETTINGS = [
        'contact.email',
        'contact.phone_1',
        'contact.phone_2',
        'contact.phone_3',
        'contact.whatsapp',
        'contact.address_full',
        'contact.hours',
        'contact.hours_lines',
        'brand.name',
        'brand.legal_name',
        'brand.tagline',
        'social.instagram',
        'social.facebook',
        'social.linkedin',
        'whatsapp.number',
        'whatsapp.open_message',
        'process.case_local',
        'agenda',
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // O zoker/responsive-images regista o alias `responsive-image` para a
        // classe dele (que tem precedência sobre a vista da aplicação). Apontamos
        // o alias para a nossa vista: é ela que sabe de `preset`, `fetchpriority`,
        // `fallback` (onerror) e que mantém o <img> de fallback no original
        // png/jpeg/jpg. Registamos depois do boot de TODOS os providers para não
        // ser sobreposto pelo pacote. Assinatura: Blade::component($classe, $alias).
        $this->app->booted(function (): void {
            Blade::component('components.responsive-image', 'responsive-image');
        });

        try {
            $site = Setting::many(self::SETTINGS);
        } catch (\Throwable $e) {
            // BD ainda sem a tabela `settings` (ex.: primeiro `migrate`).
            $site = array_fill_keys(self::SETTINGS, null);
        }

        View::share('site', $site);

        // AGENDA_CONFIG do front-end, normalizado de `settings.agenda` (B8).
        try {
            $agendaJs = (new AgendaService)->config();
        } catch (\Throwable) {
            $agendaJs = [];
        }

        View::share('agendaJs', $agendaJs);
    }
}
