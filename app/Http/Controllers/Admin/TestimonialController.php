<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $testemunhos = Testimonial::orderBy('sort_order')->orderBy('created_at')->get();

        return view('admin.testemunhos.index', ['testemunhos' => $testemunhos]);
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testemunhos.edit', ['testemunho' => $testimonial]);
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $dados = $request->validate([
            'name' => ['required', 'max:150'],
            'role' => ['nullable', 'max:150'],
            'content' => ['required'],
            'status' => ['required', Rule::in(['hidden', 'published'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'name' => 'nome',
            'role' => 'cargo',
            'content' => 'testemunho',
            'status' => 'estado',
            'sort_order' => 'ordem',
        ]);

        $dados['sort_order'] = max(0, (int) $request->input('sort_order', 0));
        $dados['role'] = trim((string) ($dados['role'] ?? '')) !== '' ? $dados['role'] : null;

        $testimonial->update($dados);

        return redirect()->route('testemunhos.index')->with('ok', 'Testemunho atualizado.');
    }

    public function toggle(Testimonial $testimonial): RedirectResponse
    {
        $publicado = $testimonial->status !== 'published';

        $testimonial->update(['status' => $publicado ? 'published' : 'hidden']);

        return redirect()->route('testemunhos.index')
            ->with('ok', $publicado ? 'Testemunho publicado.' : 'Testemunho ocultado.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('testemunhos.index')->with('ok', 'Testemunho apagado.');
    }
}
