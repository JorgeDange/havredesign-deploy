<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioGallery;
use App\Models\PortfolioItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /** Categorias do enum `portfolio_items.category` (backend.md §4.5). */
    private const CATEGORIAS = ['Residencial', 'Comercial', 'Corporativo', 'Outro'];

    /** Estados do enum `portfolio_items.status`. */
    private const ESTADOS = ['draft', 'published'];

    /**
     * Opções do enum `portfolio_items.segment` — copiadas à letra
     * (acentuação tem de coincidir com a BD).
     *
     * @var list<string>
     */
    private const SEGMENTOS = [
        'Investimento imobiliário residencial',
        'Habitação própria',
        'Comércio e serviços',
        'Outro',
    ];

    public function index(): View
    {
        $projetos = PortfolioItem::orderBy('sort_order')->orderBy('title')->get();

        return view('admin.portfolio.index', ['projetos' => $projetos]);
    }

    public function create(): View
    {
        return view('admin.portfolio.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validar($request);

        $dados['slug'] = $this->resolverSlug($dados['slug'] ?? null, $dados['title']);
        $dados['featured'] = $request->boolean('featured');
        $dados['sort_order'] = (int) ($request->input('sort_order') ?? 0);

        if ($request->hasFile('image')) {
            $dados['image_url'] = 'storage/'.$request->file('image')->store('portfolio', 'public');
        }

        $portfolio = PortfolioItem::create($dados);

        return redirect()
            ->route('portfolio.edit', $portfolio)
            ->with('ok', 'Projeto criado com sucesso.');
    }

    public function edit(PortfolioItem $portfolio): View
    {
        $portfolio->load(['gallery' => fn ($q) => $q->orderBy('sort_order')]);

        return view('admin.portfolio.edit', ['portfolio' => $portfolio]);
    }

    public function update(Request $request, PortfolioItem $portfolio): RedirectResponse
    {
        $dados = $this->validar($request, $portfolio);

        // Slug vazio no update mantém o URL atual (preserva ligações existentes).
        if (array_key_exists('slug', $dados)) {
            if (filled($dados['slug'])) {
                $dados['slug'] = $this->resolverSlug($dados['slug'], $dados['title'], $portfolio);
            } else {
                unset($dados['slug']);
            }
        }

        $dados['featured'] = $request->boolean('featured');
        $dados['sort_order'] = (int) ($request->input('sort_order') ?? 0);

        if ($request->hasFile('image')) {
            $this->apagarFicheiro($portfolio->image_url);

            $dados['image_url'] = 'storage/'.$request->file('image')->store('portfolio', 'public');
        }

        $portfolio->update($dados);

        return redirect()
            ->route('portfolio.edit', $portfolio)
            ->with('ok', 'Projeto atualizado com sucesso.');
    }

    public function destroy(PortfolioItem $portfolio): RedirectResponse
    {
        $this->apagarFicheiro($portfolio->image_url);

        // As linhas de `portfolio_gallery` caem em cascata (FK ON DELETE CASCADE);
        // os ficheiros das imagens são apagados primeiro, para não ficarem órfãos.
        foreach ($portfolio->gallery as $imagem) {
            $this->apagarFicheiro($imagem->image_url);
        }

        $portfolio->delete();

        return redirect()
            ->route('portfolio.index')
            ->with('ok', 'Projeto removido do portefólio.');
    }

    /**
     * Upload múltiplo de imagens para a galeria (apenas na página de edição).
     */
    public function galeria(Request $request, PortfolioItem $portfolio): RedirectResponse
    {
        $request->validate([
            'imagens' => ['required', 'array', 'max:10'],
            'imagens.*' => ['image', 'max:5120'],
        ], [], [
            'imagens' => 'imagens',
            'imagens.*' => 'imagem',
        ]);

        $ordem = (int) $portfolio->gallery()->max('sort_order');

        foreach ($request->file('imagens', []) as $ficheiro) {
            $caminho = $ficheiro->store('portfolio', 'public');

            $portfolio->gallery()->create([
                'image_url' => 'storage/'.$caminho,
                'sort_order' => ++$ordem,
            ]);
        }

        return redirect()
            ->route('portfolio.edit', $portfolio)
            ->with('ok', 'Imagens adicionadas.');
    }

    /**
     * Remove uma imagem da galeria. A rota não tem model binding:
     * {galeria} é o id NUMÉRICO da tabela `portfolio_gallery`.
     */
    public function apagarGaleria(string $galeria): RedirectResponse
    {
        $imagem = PortfolioGallery::findOrFail((int) $galeria);

        $this->apagarFicheiro($imagem->image_url);

        $imagem->delete();

        return redirect()
            ->route('portfolio.edit', $imagem->portfolio_id)
            ->with('ok', 'Imagem removida da galeria.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validar(Request $request, ?PortfolioItem $portfolio = null): array
    {
        $slug = ['nullable', 'bail', 'string', 'max:200'];

        if (filled($request->input('slug'))) {
            $regraUnica = Rule::unique('portfolio_items', 'slug');

            if ($portfolio !== null) {
                $regraUnica->ignore($portfolio->getKey());
            }

            $slug[] = $regraUnica;
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => $slug,
            'category' => ['required', 'bail', Rule::in(self::CATEGORIAS)],
            'status' => ['required', 'bail', Rule::in(self::ESTADOS)],
            'segment' => ['nullable', 'bail', Rule::in(self::SEGMENTOS)],
            'description' => ['required', 'string'],
            'area' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'between:2000,2030'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'title' => 'título',
            'slug' => 'slug',
            'category' => 'categoria',
            'status' => 'estado',
            'segment' => 'segmento',
            'description' => 'descrição',
            'area' => 'área',
            'year' => 'ano',
            'location' => 'localização',
            'image' => 'imagem de capa',
            'featured' => 'destaque',
            'sort_order' => 'ordem',
        ]);
    }

    /**
     * Slug: vazio no store gera a partir do título; nunca repete um slug existente.
     */
    private function resolverSlug(?string $slug, string $titulo, ?PortfolioItem $portfolio = null): string
    {
        $base = filled($slug) ? Str::slug($slug) : Str::slug($titulo);

        if ($base === '') {
            $base = 'projeto';
        }

        $final = $base;
        $sufixo = 2;

        while (PortfolioItem::where('slug', $final)
            ->when($portfolio !== null, fn ($q) => $q->whereKeyNot($portfolio->getKey()))
            ->exists()) {
            $final = $base.'-'.$sufixo;
            $sufixo++;
        }

        return $final;
    }

    /**
     * Apaga imagens do disco público (só caminhos guardados por upload).
     */
    private function apagarFicheiro(?string $url): void
    {
        if (is_string($url) && str_starts_with($url, 'storage/')) {
            Storage::disk('public')->delete(substr($url, strlen('storage/')));
        }
    }
}
