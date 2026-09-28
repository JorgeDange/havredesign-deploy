<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registar', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('registar', [RegisteredUserController::class, 'store']);

    Route::get('entrar', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('entrar', [AuthenticatedSessionController::class, 'store']);

    Route::get('recuperar-palavra-passe', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('recuperar-palavra-passe', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('redefinir-palavra-passe/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('redefinir-palavra-passe', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verificar-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verificar-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('verificar-email/notificacao', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirmar-palavra-passe', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirmar-palavra-passe', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('sair', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
