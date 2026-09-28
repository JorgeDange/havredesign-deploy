<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * B10 — Utilizadores do painel (papel USER/ADMIN) — laravel.md B10 / backend.md B3.
 * `role` está fora do $fillable de propósito: atribuição direta + save().
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.utilizadores.index', [
            'utilizadores' => User::orderBy('name')->get(),
        ]);
    }

    public function papel(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('erro', 'Não pode alterar o próprio papel.');
        }

        $user->role = $user->role === 'ADMIN' ? 'USER' : 'ADMIN';
        $user->save();

        return back()->with('ok', 'Papel atualizado para '.$user->role.'.');
    }
}
