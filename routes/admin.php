<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\ProjectRequestController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| B10 — Administração em Blade (laravel.md B10 / backend.md §7)
|--------------------------------------------------------------------------
| TODAS as rotas exigem auth + papel ADMIN (role:ADMIN, B3).
| Formulǭrios usam POST + @method('PUT'|'DELETE') (spoofing) + CSRF.
| O prefixo vem de config('admin.path') (ADMIN_PATH no .env).
*/

Route::middleware(['auth', 'role:ADMIN'])->prefix(config('admin.path'))->group(function () {
    // Painel (mantǸm o name 'admin' usado no header/conta desde a B5/B9).
    Route::get('/', [DashboardController::class, 'index'])->name('admin');

    // Serviços (+ "inclui" gerido no formulário de edição, uma linha por item).
    Route::get('/servicos', [ServiceController::class, 'index'])->name('servicos.index');
    Route::get('/servicos/novo', [ServiceController::class, 'create'])->name('servicos.create');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('servicos.store');
    Route::get('/servicos/{service}', [ServiceController::class, 'edit'])->name('servicos.edit');
    Route::put('/servicos/{service}', [ServiceController::class, 'update'])->name('servicos.update');
    Route::post('/servicos/{service}/alternar', [ServiceController::class, 'toggle'])->name('servicos.toggle');
    Route::delete('/servicos/{service}', [ServiceController::class, 'destroy'])->name('servicos.destroy');

    // Portefólio (+ capa e galeria por upload em disco público).
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/portfolio/novo', [PortfolioController::class, 'create'])->name('portfolio.create');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::get('/portfolio/{portfolio}', [PortfolioController::class, 'edit'])->name('portfolio.edit');
    Route::put('/portfolio/{portfolio}', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('/portfolio/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    Route::post('/portfolio/{portfolio}/galeria', [PortfolioController::class, 'galeria'])->name('portfolio.galeria');
    Route::post('/galeria/{galeria}/apagar', [PortfolioController::class, 'apagarGaleria'])->name('portfolio.galeria.apagar');

    // Soluções comerciais (tabela solutions).
    Route::get('/solucoes', [SolutionController::class, 'index'])->name('solucoes.index');
    Route::get('/solucoes/nova', [SolutionController::class, 'create'])->name('solucoes.create');
    Route::post('/solucoes', [SolutionController::class, 'store'])->name('solucoes.store');
    Route::get('/solucoes/{solution}', [SolutionController::class, 'edit'])->name('solucoes.edit');
    Route::put('/solucoes/{solution}', [SolutionController::class, 'update'])->name('solucoes.update');
    Route::delete('/solucoes/{solution}', [SolutionController::class, 'destroy'])->name('solucoes.destroy');

    // Pedidos de orçamento (mudar status + ver detalhe e anexos).
    Route::get('/pedidos', [ProjectRequestController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{projectRequest}', [ProjectRequestController::class, 'show'])->name('pedidos.show');
    Route::get('/pedidos/{projectRequest}/anexos/{attachment}', [ProjectRequestController::class, 'download'])->name('pedidos.anexo.download');
    Route::get('/pedidos/{projectRequest}/anexos/{attachment}/ver', [ProjectRequestController::class, 'preview'])->name('pedidos.anexo.preview');
    Route::put('/pedidos/{projectRequest}', [ProjectRequestController::class, 'update'])->name('pedidos.update');

    // Agendamentos (status PENDING/CONFIRMED/CANCELLED + e-mail ao cliente).
    Route::get('/agendamentos', [AppointmentController::class, 'index'])->name('agendamentos.index');
    Route::get('/agendamentos/{appointment}', [AppointmentController::class, 'show'])->name('agendamentos.show');
    Route::put('/agendamentos/{appointment}', [AppointmentController::class, 'update'])->name('agendamentos.update');

    // Mensagens de contacto (status new/replied/closed).
    Route::get('/mensagens', [ContactMessageController::class, 'index'])->name('mensagens.index');
    Route::get('/mensagens/{contactMessage}', [ContactMessageController::class, 'show'])->name('mensagens.show');
    Route::put('/mensagens/{contactMessage}', [ContactMessageController::class, 'update'])->name('mensagens.update');
    Route::delete('/mensagens/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('mensagens.destroy');

    // Testemunhos (aprovação de conteúdos — D6).
    Route::get('/testemunhos', [TestimonialController::class, 'index'])->name('testemunhos.index');
    Route::get('/testemunhos/{testimonial}/editar', [TestimonialController::class, 'edit'])->name('testemunhos.edit');
    Route::put('/testemunhos/{testimonial}', [TestimonialController::class, 'update'])->name('testemunhos.update');
    Route::post('/testemunhos/{testimonial}/alternar', [TestimonialController::class, 'toggle'])->name('testemunhos.toggle');
    Route::delete('/testemunhos/{testimonial}', [TestimonialController::class, 'destroy'])->name('testemunhos.destroy');

    // Definições (settings: contacto, marca, redes, WhatsApp, agenda).
    Route::get('/definicoes', [SettingController::class, 'index'])->name('definicoes.index');
    Route::post('/definicoes', [SettingController::class, 'update'])->name('definicoes.update');

    // Utilizadores (papel USER/ADMIN).
    Route::get('/utilizadores', [UserController::class, 'index'])->name('utilizadores.index');
    Route::post('/utilizadores/{user}/papel', [UserController::class, 'papel'])->name('utilizadores.papel');
});
