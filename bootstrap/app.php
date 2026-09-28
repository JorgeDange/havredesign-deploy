<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\RedirectLegacyUrls;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        // B12 — cabeçalhos de segurança em todas as respostas.
        $middleware->append(SecurityHeaders::class);

        // B12.4 — 301 da tabela `redirects`, antes do router (apanha URLs que hoje dão 404).
        $middleware->append(RedirectLegacyUrls::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
