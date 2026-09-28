@extends('admin.layout')

@section('title', 'Novo projeto - Administração HAVREDESIGN')
@section('titulo', 'Novo projeto')
@section('subtitulo', 'Adicionar um projeto ao portefólio')

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="post" action="{{ route('portfolio.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2 sm:col-span-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200" value="{{ old('title') }}"
               placeholder="Ex.: Moradia com vista para o mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('title')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug</label>
        <p class="text-xs text-muted-foreground -mt-1">Em branco, gera automaticamente a partir do título.</p>
        <input id="slug" name="slug" type="text" maxlength="200" value="{{ old('slug') }}"
               placeholder="moradia-vista-mar"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('slug')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="category" class="block text-sm font-medium text-foreground">Categoria *</label>
        <select id="category" name="category" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">Selecione a categoria</option>
          <option value="Residencial" @selected(old('category') === 'Residencial')>Residencial</option>
          <option value="Comercial" @selected(old('category') === 'Comercial')>Comercial</option>
          <option value="Corporativo" @selected(old('category') === 'Corporativo')>Corporativo</option>
          <option value="Outro" @selected(old('category') === 'Outro')>Outro</option>
        </select>
        @error('category')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="status" class="block text-sm font-medium text-foreground">Estado *</label>
        <select id="status" name="status" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="published" @selected(old('status', 'published') === 'published')>Publicado</option>
          <option value="draft" @selected(old('status', 'published') === 'draft')>Rascunho</option>
        </select>
        @error('status')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="segment" class="block text-sm font-medium text-foreground">Segmento</label>
        <select id="segment" name="segment"
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="">—</option>
          <option value="Investimento imobiliário residencial" @selected(old('segment') === 'Investimento imobiliário residencial')>Investimento imobiliário residencial</option>
          <option value="Habitação própria" @selected(old('segment') === 'Habitação própria')>Habitação própria</option>
          <option value="Comércio e serviços" @selected(old('segment') === 'Comércio e serviços')>Comércio e serviços</option>
          <option value="Outro" @selected(old('segment') === 'Outro')>Outro</option>
        </select>
        @error('segment')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="area" class="block text-sm font-medium text-foreground">Área</label>
        <input id="area" name="area" type="text" maxlength="50" value="{{ old('area') }}"
               placeholder="Ex.: 450m2"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('area')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="year" class="block text-sm font-medium text-foreground">Ano</label>
        <input id="year" name="year" type="number" min="2000" max="2030" step="1" value="{{ old('year') }}"
               placeholder="2000 — 2030"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('year')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="location" class="block text-sm font-medium text-foreground">Localização</label>
        <input id="location" name="location" type="text" maxlength="255" value="{{ old('location') }}"
               placeholder="Ex.: Talatona, Luanda"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('location')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <p class="text-xs text-muted-foreground -mt-1">Ordenação na listagem pública (0 = primeiro).</p>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', 0) }}"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('sort_order')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2 sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
        <textarea id="description" name="description" required rows="6"
                  placeholder="Descreva o projeto: tipologia, área, materiais e diferenciais."
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('description') }}</textarea>
        @error('description')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label class="flex items-center gap-3 text-sm text-foreground">
          <input type="hidden" name="featured" value="0" />
          <input id="featured" name="featured" type="checkbox" value="1" @checked(old('featured', false))
                 class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
          <span>Destacar no Início</span>
        </label>
        @error('featured')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="image" class="block text-sm font-medium text-foreground">Imagem de capa</label>
        <input id="image" name="image" type="file" accept="image/*"
               class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
        <p class="text-xs text-muted-foreground">JPG, PNG ou WEBP até 5 MB.</p>
        @error('image')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar projeto
      </button>
      <a href="{{ route('portfolio.index') }}" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
