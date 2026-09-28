<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SolutionController extends Controller
{
    public function index(): View
    {
        $solucoes = Solution::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.solucoes.index', ['solucoes' => $solucoes]);
    }

    public function create(): View
    {
        return view('admin.solucoes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->preencherSlug($request);

        $dados = $request->validate($this->regras(), [], $this->atributos());

        $dados['active'] = $request->boolean('active');
        $dados['sort_order'] = max(0, (int) $request->input('sort_order', 0));
        $dados['cta_label'] = trim((string) ($dados['cta_label'] ?? '')) !== '' ? $dados['cta_label'] : null;

        Solution::create($dados);

        return redirect()->route('solucoes.index')->with('ok', 'Solução criada.');
    }

    public function edit(Solution $solution): View
    {
        return view('admin.solucoes.edit', ['solucao' => $solution]);
    }

    public function update(Request $request, Solution $solution): RedirectResponse
    {
        $dados = $request->validate($this->regras($solution), [], $this->atributos());

        $dados['active'] = $request->boolean('active');
        $dados['sort_order'] = max(0, (int) $request->input('sort_order', 0));
        $dados['cta_label'] = trim((string) ($dados['cta_label'] ?? '')) !== '' ? $dados['cta_label'] : null;

        $solution->update($dados);

        return redirect()->route('solucoes.index')->with('ok', 'Solução atualizada.');
    }

    public function destroy(Solution $solution): RedirectResponse
    {
        $solution->delete();

        return redirect()->route('solucoes.index')->with('ok', 'Solução apagada.');
    }

    /**
     * Slug vazio na criação → derivado do nome, antes da validação.
     */
    private function preencherSlug(Request $request): void
    {
        if (! $request->filled('slug')) {
            $request->merge(['slug' => Str::slug((string) $request->input('name'))]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function regras(?Solution $solution = null): array
    {
        $regras = [
            'code' => ['required', 'max:20', Rule::unique('solutions', 'code')],
            'name' => ['required', 'max:120'],
            'slug' => ['required', 'max:120', Rule::unique('solutions', 'slug')],
            'description' => ['required'],
            'cta_label' => ['nullable', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ];

        if ($solution !== null) {
            $regras['code'][2] = Rule::unique('solutions', 'code')->ignore($solution->getKey());
            $regras['slug'][2] = Rule::unique('solutions', 'slug')->ignore($solution->getKey());
        }

        return $regras;
    }

    /**
     * @return array<string, string>
     */
    private function atributos(): array
    {
        return [
            'code' => 'código',
            'name' => 'nome',
            'slug' => 'slug',
            'description' => 'descrição',
            'cta_label' => 'texto do botão',
            'sort_order' => 'ordem',
            'active' => 'ativo',
        ];
    }
}
