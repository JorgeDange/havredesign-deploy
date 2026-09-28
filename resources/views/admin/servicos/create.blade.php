@extends('admin.layout')

@section('title', 'Novo Serviço - Administração HAVREDESIGN')
@section('titulo', 'Novo serviço')
@section('subtitulo', 'Criar um serviço principal ou complementar.')

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="{{ route('servicos.store') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="title" class="block text-sm font-medium text-foreground">Título *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="{{ old('title') }}"
               placeholder="Ex.: Projeto Arquitetónico"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('title')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug *</label>
        <input id="slug" name="slug" type="text" required maxlength="200"
               value="{{ old('slug') }}"
               placeholder="Ex.: projeto-arquitetonico"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Identificador único no endereço do site.</p>
        @error('slug')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="space-y-2">
        <label for="group" class="block text-sm font-medium text-foreground">Grupo *</label>
        <select id="group" name="group" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="principal" @selected(old('group') === 'principal')>Principal</option>
          <option value="complementar" @selected(old('group') === 'complementar')>Complementar</option>
        </select>
        @error('group')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="icon" class="block text-sm font-medium text-foreground">Ícone</label>
        <input id="icon" name="icon" type="text" maxlength="50"
               value="{{ old('icon') }}"
               placeholder="Ex.: home"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Nome do ícone (máx. 50 caracteres).</p>
        @error('icon')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
               value="{{ old('sort_order', 0) }}"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('sort_order')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="space-y-2">
      <label for="description" class="block text-sm font-medium text-foreground">Descrição *</label>
      <textarea id="description" name="description" rows="5" required
                placeholder="Descreva o serviço apresentado no site."
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('description') }}</textarea>
      @error('description')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-2">
      <label for="image" class="block text-sm font-medium text-foreground">Imagem</label>
      <input id="image" name="image" type="file" accept="image/*"
             class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-secondary file:text-secondary-foreground file:text-sm file:font-medium hover:file:bg-secondary/90" />
      <p class="text-xs text-muted-foreground">Formatos de imagem, até 5 MB.</p>
      @error('image')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-3 text-sm text-foreground">
      <input type="hidden" name="active" value="0" />
      <input id="active" name="active" type="checkbox" value="1" @checked(old('active', true))
             class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
      <span>Serviço ativo (visível no site)</span>
    </label>
    @error('active')<p class="text-xs text-destructive">{{ $message }}</p>@enderror

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar serviço
      </button>
      <a href="{{ route('servicos.index') }}" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
