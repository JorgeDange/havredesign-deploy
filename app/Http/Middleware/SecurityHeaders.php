<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * B12 — cabeçalhos de segurança (laravel.md §B12).
 *
 * `X-XSS-Protection: 0` é propositado: o motor de XSS do IE é um vector de
 * ataques; os navegadores modernos não o usam. O HSTS só faz sentido em
 * produção — em `http://` os navegadores ignoram-no, mas envia-lo em ambiente
 * de testes sobre https faria o browser guardar o domínio como seguro.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '0',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), geolocation=(), microphone=(), payment=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        if (config('app.env') === 'production') {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        // Não expor a versão do PHP (expose_php). No-op se o INI já o desliga.
        $response->headers->remove('X-Powered-By');
        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }

        return $response;
    }
}
