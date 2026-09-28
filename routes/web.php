<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProjectRequestController;
use App\Http\Controllers\SeoController;
use App\Services\AgendaService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas públicas (B5 — scaffold; os controllers entram em B6/B7)
|--------------------------------------------------------------------------
| Mapeamento completo: laravel.md §5.
*/

Route::get('/', function () {
    return view('home.index');
})->name('home');

Route::get('/sobre', function () {
    return view('pages.about');
})->name('sobre');

Route::get('/servicos', function () {
    return view('services.index');
})->name('servicos');

Route::get('/orcamentos', function () {
    return view('solutions.index');
})->name('orcamentos');

Route::get('/portfolio', function () {
    return view('portfolio.index');
})->name('portfolio');

Route::get('/portfolio/{slug}', function (string $slug) {
    return view('portfolio.show', ['slug' => $slug]);
})->name('portfolio.show');

Route::get('/processo', function () {
    return view('pages.process');
})->name('processo');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/contacto', function () {
    return view('contact.index');
})->name('contacto');

// B7 — formulários reais (laravel.md B7).
Route::post('/contacto', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contacto.store');

Route::get('/solicitar-projeto', function () {
    return view('project.create');
})->name('solicitar-projeto');

Route::post('/solicitar-projeto', [ProjectRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('solicitar-projeto.store');

Route::get('/agendar', function () {
    return view('booking.create');
})->name('agendar');

// B8 — agenda real (laravel.md B8 / backend.md §7).
Route::get('/agendar/disponibilidade', [AppointmentController::class, 'availability'])
    ->name('agendar.disponibilidade');

Route::post('/agendar', [AppointmentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('agendar.store');

Route::get('/politica-de-privacidade', function () {
    return view('legal.privacy');
})->name('privacidade');

Route::get('/termos-de-uso', function () {
    return view('legal.terms');
})->name('termos');

// /networking — URL permanente do QR Code institucional.
// Nunca redireciona (301) nem é renomeada: o QR impresso tem de continuar a funcionar.
Route::view('/networking', 'networking')->name('networking');

// B12.4 — SEO: gerados da config (`app.url`) e da BD (SeoController).
// São rotas, não ficheiros em public/: assim o domínio canónico vem do .env
// e o sitemap acompanha o portefólio publicado sem cópias manuais.
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| B9 — autenticação (Breeze) e área do cliente
|--------------------------------------------------------------------------
| URIs em PT (backend.md §6: /entrar, /registar, /recuperar-palavra-passe);
| os *names* mantêm os do Breeze por os controllers redirecionarem por eles.
*/

Route::middleware('auth')->group(function () {
    // Conta do cliente — vê só os seus pedidos e agendamentos.
    Route::get('/conta', function () {
        $user = auth()->user();

        return view('conta.index', [
            'user' => $user,
            'pedidos' => $user->projectRequests()->latest()->get(),
            'agendamentos' => $user->appointments()
                ->orderBy('appt_date')
                ->orderBy('appt_time')
                ->get(),
            'tipos' => app(AgendaService::class)->config()['tipos'],
        ]);
    })->name('dashboard');

    // B10 — o painel vive em routes/admin.php (role:ADMIN, prefixo ADMIN_PATH).
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
