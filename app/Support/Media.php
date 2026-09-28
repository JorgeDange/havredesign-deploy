<?php

namespace App\Support;

/**
 * Resolve caminhos de imagem guardados na BD para URLs públicas (B10).
 *
 * A BD guarda caminhos relativos a public/ (ex.: 'assets/img/x.jpg' no seed
 * antigo, 'storage/portfolio/x.jpg' em uploads novos) — mesmo contrato do
 * helper $imagem das vistas públicas: asset(ltrim($url, '/')).
 */
class Media
{
    public static function url(?string $url, string $fallback = ''): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return $fallback !== '' ? asset($fallback) : '';
        }

        return asset(ltrim($url, '/'));
    }

    public static function thumbnail(?string $url): string
    {
        return self::url($url, 'assets/hero-section.png');
    }
}
