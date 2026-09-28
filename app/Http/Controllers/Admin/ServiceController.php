<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceInclude;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $servicos = Service::orderBy('sort_order')->orderBy('title')->get();

        return view('admin.servicos.index', ['servicos' => $servicos]);
    }

    public function create(): View
    {
        return view('admin.servicos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate($this->regras(), [], $this->atributos());

        $dados['active'] = $request->boolean('active');
        $dados['sort_order'] = max(0, (int) $request->input('sort_order', 0));

        $imagem = $this->guardarImagem($request);
        if ($imagem !== null) {
            $dados['image_url'] = $imagem;
        }

        Service::create($dados);

        return redirect()->route('servicos.index')->with('ok', 'Serviço criado.');
    }

    public function edit(Service $service): View
    {
        $inclui = ServiceInclude::where('service_id', $service->getKey())
            ->orderBy('sort_order')
            ->pluck('item')
            ->implode("\n");

        return view('admin.servicos.edit', [
            'servico' => $service,
            'inclui' => old('inclui', $inclui),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $dados = $request->validate($this->regras($service), [], $this->atributos());

        $imagemAntiga = (string) $service->image_url;
        $dados['active'] = $request->boolean('active');
        $dados['sort_order'] = max(0, (int) $request->input('sort_order', 0));

        $imagem = $this->guardarImagem($request);
        if ($imagem !== null) {
            $dados['image_url'] = $imagem;
            $this->apagarImagem($imagemAntiga);
        }

        unset($dados['inclui']); // tratado em sincronizarInclui()
        $service->update($dados);
        $this->sincronizarInclui($request, $service);

        return redirect()->route('servicos.edit', $service)->with('ok', 'Serviço atualizado.');
    }

    public function toggle(Service $service): RedirectResponse
    {
        $ativado = ! $service->active;

        $service->update(['active' => $ativado]);

        return redirect()->route('servicos.index')
            ->with('ok', $ativado ? 'Serviço ativado.' : 'Serviço desativado.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $imagem = (string) $service->image_url;

        $service->delete();
        $this->apagarImagem($imagem);

        return redirect()->route('servicos.index')->with('ok', 'Serviço apagado.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function regras(?Service $service = null): array
    {
        $regras = [
            'group' => ['required', 'in:principal,complementar'],
            'title' => ['required', 'max:200'],
            'slug' => ['required', 'max:200', Rule::unique('services', 'slug')],
            'description' => ['required'],
            'icon' => ['nullable', 'max:50'],
            'image' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'inclui' => ['nullable', 'string'],
        ];

        if ($service !== null) {
            $regras['slug'][2] = Rule::unique('services', 'slug')->ignore($service->getKey());
        }

        return $regras;
    }

    /**
     * @return array<string, string>
     */
    private function atributos(): array
    {
        return [
            'group' => 'grupo',
            'title' => 'título',
            'slug' => 'slug',
            'description' => 'descrição',
            'icon' => 'ícone',
            'image' => 'imagem',
            'sort_order' => 'ordem',
            'active' => 'ativo',
            'inclui' => 'inclui',
        ];
    }

    private function guardarImagem(Request $request): ?string
    {
        if (! $request->hasFile('image') || ! $request->file('image')->isValid()) {
            return null;
        }

        $ficheiro = $request->file('image');
        $nome = Str::random(16) . '.' . $ficheiro->extension();
        $ficheiro->storeAs('services', $nome, 'public');

        return 'storage/services/' . $nome;
    }

    private function apagarImagem(?string $caminho): void
    {
        $caminho = (string) $caminho;

        if (str_starts_with($caminho, 'storage/')) {
            Storage::disk('public')->delete(substr($caminho, strlen('storage/')));
        }
    }

    /**
     * Sincroniza a textarea «inclui» (um item por linha) com a tabela
     * service_includes: remove os que já não estão, acrescenta os novos e
     * renumera sort_order por ordem de apresentação.
     */
    private function sincronizarInclui(Request $request, Service $service): void
    {
        if (! $request->has('inclui')) {
            return;
        }

        $itens = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $request->input('inclui')) ?: [] as $linha) {
            $linha = trim($linha);
            if ($linha !== '' && ! in_array($linha, $itens, true)) {
                $itens[] = $linha;
            }
        }

        $existentes = ServiceInclude::where('service_id', $service->getKey())
            ->orderBy('sort_order')
            ->get()
            ->keyBy('item');

        foreach ($existentes as $item) {
            if (! in_array($item->item, $itens, true)) {
                $item->delete();
            }
        }

        $ordem = 1;
        foreach ($itens as $texto) {
            if (isset($existentes[$texto])) {
                $existentes[$texto]->update(['sort_order' => $ordem]);
            } else {
                ServiceInclude::create([
                    'service_id' => $service->getKey(),
                    'item' => $texto,
                    'sort_order' => $ordem,
                ]);
            }

            $ordem++;
        }
    }
}
