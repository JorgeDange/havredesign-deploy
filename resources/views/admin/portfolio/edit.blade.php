@extends('admin.layout')

@section('title', 'Editar projeto - Administração HAVREDESIGN')
@section('titulo', 'Editar projeto')
@section('subtitulo', $portfolio->title)

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="post" action="{{ route('portfolio.update', $portfolio) }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2 sm:col-span-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200" value="{{ old('title', $portfolio->title) }}"
               placeholder="Ex.: Moradia com vista para o mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('title')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug</label>
        <p class="text-xs text-muted-foreground -mt-1">Em branco mantém o URL atual.</p>
        <input id="slug" name="slug" type="text" maxlength="200" value="{{ old('slug', $portfolio->slug) }}"
               placeholder="moradia-vista-mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('slug')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="category" class="block text-sm font-medium text-foreground">Categoria *</label>
        <select id="category" name="category" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">Selecione a categoria</option>
          <option value="Residencial" @selected(old('category', $portfolio->category) === 'Residencial')>Residencial</option>
          <option value="Comercial" @selected(old('category', $portfolio->category) === 'Comercial')>Comercial</option>
          <option value="Corporativo" @selected(old('category', $portfolio->category) === 'Corporativo')>Corporativo</option>
          <option value="Outro" @selected(old('category', $portfolio->category) === 'Outro')>Outro</option>
        </select>
        @error('category')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="status" class="block text-sm font-medium text-foreground">Estado *</label>
        <select id="status" name="status" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="published" @selected(old('status', $portfolio->status) === 'published')>Publicado</option>
          <option value="draft" @selected(old('status', $portfolio->status) === 'draft')>Rascunho</option>
        </select>
        @error('status')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="segment" class="block text-sm font-medium text-foreground">Segmento</label>
        <select id="segment" name="segment"
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">—</option>
          <option value="Investimento imobiliário residencial" @selected(old('segment', $portfolio->segment) === 'Investimento imobiliário residencial')>Investimento imobiliário residencial</option>
          <option value="Habitação própria" @selected(old('segment', $portfolio->segment) === 'Habitação própria')>Habitação própria</option>
          <option value="Comércio e serviços" @selected(old('segment', $portfolio->segment) === 'Comércio e serviços')>Comércio e serviços</option>
          <option value="Outro" @selected(old('segment', $portfolio->segment) === 'Outro')>Outro</option>
        </select>
        @error('segment')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="area" class="block text-sm font-medium text-foreground">Área</label>
        <input id="area" name="area" type="text" maxlength="50" value="{{ old('area', $portfolio->area) }}"
               placeholder="Ex.: 450m2"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('area')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="year" class="block text-sm font-medium text-foreground">Ano</label>
        <input id="year" name="year" type="number" min="2000" max="2030" step="1" value="{{ old('year', $portfolio->year) }}"
               placeholder="2000 — 2030"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('year')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="location" class="block text-sm font-medium text-foreground">Localização</label>
        <input id="location" name="location" type="text" maxlength="255" value="{{ old('location', $portfolio->location) }}"
               placeholder="Ex.: Talatona, Luanda"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('location')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <p class="text-xs text-muted-foreground -mt-1">Ordenação na listagem pública (0 = primeiro).</p>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $portfolio->sort_order) }}"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('sort_order')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2 sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
        <textarea id="description" name="description" required rows="6"
                  placeholder="Descreva o projeto: tipologia, área, materiais e diferenciais."
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('description', $portfolio->description) }}</textarea>
        @error('description')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label class="flex items-center gap-3 text-sm text-foreground">
          <input type="hidden" name="featured" value="0" />
          <input id="featured" name="featured" type="checkbox" value="1" @checked(old('featured', $portfolio->featured))
                 class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
          <span>Destacar no Início</span>
        </label>
        @error('featured')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="image" class="block text-sm font-medium text-foreground">Imagem de capa</label>
        @if ($portfolio->image_url)
          <img src="{{ \App\Support\Media::thumbnail($portfolio->image_url) }}" alt="{{ $portfolio->title }}"
               class="h-24 w-36 rounded-md object-cover bg-muted border border-border" />
        @endif
        <input id="image" name="image" type="file" accept="image/*"
               class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
        <p class="text-xs text-muted-foreground">JPG, PNG ou WEBP até 5 MB. Substituir a imagem apaga a atual.</p>
        @error('image')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar alterações
      </button>
      <a href="{{ route('portfolio.index') }}" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Voltar
      </a>
    </div>
  </form>
</div>

<div class="bg-card border border-border rounded-xl shadow-sm p-6 mt-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold text-foreground">Galeria</h2>
    <span class="text-xs font-medium px-2 py-1 rounded-full bg-muted text-muted-foreground">
      {{ $portfolio->gallery->count() }} imagem(ns)
    </span>
  </div>

  @if ($portfolio->gallery->isEmpty())
    <p class="text-sm text-muted-foreground">Sem imagens na galeria deste projeto.</p>
  @else
    <ul class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      @foreach ($portfolio->gallery as $imagem)
        <li class="border border-border rounded-xl overflow-hidden bg-muted">
          <img src="{{ \App\Support\Media::url($imagem->image_url) }}"
               alt="Imagem {{ $imagem->sort_order }} de {{ $portfolio->title }}"
               class="w-full h-28 object-cover" />
          <div class="p-2 flex items-center justify-between gap-2">
            <span class="text-xs text-muted-foreground">N.º {{ $imagem->sort_order }}</span>
            <form method="post" action="{{ route('portfolio.galeria.apagar', $imagem->id) }}"
                  onsubmit="return confirm('Apagar esta imagem da galeria? Esta ação não pode ser anulada.');">
              @csrf
              <button type="submit"
                      class="px-2 py-1 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                Apagar
              </button>
            </form>
          </div>
        </li>
      @endforeach
    </ul>
  @endif

  <form method="post" action="{{ route('portfolio.galeria', $portfolio) }}" enctype="multipart/form-data"
        class="mt-6 border border-dashed border-border rounded-md p-6 bg-muted">
    @csrf
    <label for="imagens" class="block text-sm font-medium text-foreground mb-2">Adicionar imagens</label>
    <input id="imagens" name="imagens[]" type="file" multiple accept="image/*"
           class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
    <p class="text-xs text-muted-foreground mt-2">Até 10 imagens por envio, máx. 5 MB cada (JPG, PNG ou WEBP).</p>
    @error('imagens')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    @error('imagens.*')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    <button type="submit" class="mt-4 px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
      Enviar imagens
    </button>
  </form>
</div>
@endsection
