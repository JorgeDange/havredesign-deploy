<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * B12.4 — redirecionamentos 301 configuráveis pela tabela `redirects`
 * (laravel.md §B12; backend.md §4.11; hipóteses em plan.md §C).
 *
 * Corre antes do router, portanto apanha também URLs que hoje dão 404
 * (`servicos.html`, `/orcamento/pacotes`, slugs em inglês, …). Só GET/HEAD —
 * um POST nunca é reescrito, senão os formulários perderiam o corpo.
 *
 * Guarda-se contra a tabela ainda não existir (instalação mínima): um erro de
 * schema nunca pode derrubar o site por causa de um redirecionamento.
 */
class RedirectLegacyUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $source = '/'.trim($request->path(), '/');

        try {
            $redirect = Redirect::query()
                ->where('active', true)
                ->where('source_path', $source)
                ->first();
        } catch (\Throwable) {
            return $next($request);
        }

        if (! $redirect) {
            return $next($request);
        }

        return redirect()->to($redirect->target_path, (int) $redirect->status_code);
    }
}
