@extends('admin.layout')

@section('title', 'Nova Solução - Administração HAVREDESIGN')
@section('titulo', 'Nova solução')
@section('subtitulo', 'Criar uma solução comercial apresentada no site.')

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="{{ route('solucoes.store') }}" class="space-y-5">
    @csrf

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="code" class="block text-sm font-medium text-foreground">Código *</label>
        <input id="code" name="code" type="text" required maxlength="20"
               value="{{ old('code') }}"
               placeholder="Ex.: HAV-01"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Máx. 20 caracteres, tem de ser único.</p>
        @error('code')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-foreground">Nome *</label>
        <input id="name" name="name" type="text" required maxlength="120"
               value="{{ old('name') }}"
               placeholder="Ex.: HAVRE Essencial"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('name')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
      <div class="space-y-2">
        <label for="slug" class="block text-sm font-medium text-foreground">Slug</label>
        <input id="slug" name="slug" type="text" maxlength="120"
               value="{{ old('slug') }}"
               placeholder="gerado a partir do nome"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        <p class="text-xs text-muted-foreground">Omita para gerar a partir do nome. Tem de ser único.</p>
        @error('slug')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="cta_label" class="block text-sm font-medium text-foreground">Texto do botão</label>
        <input id="cta_label" name="cta_label" type="text" maxlength="60"
               value="{{ old('cta_label') }}"
               placeholder="Ex.: Solicitar orçamento"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('cta_label')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
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
                placeholder="Descreva a solução apresentada no site."
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('description') }}</textarea>
      @error('description')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-3 text-sm text-foreground">
      <input type="hidden" name="active" value="0" />
      <input id="active" name="active" type="checkbox" value="1" @checked(old('active', true))
             class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary" />
      <span>Solução ativa (visível no site)</span>
    </label>
    @error('active')<p class="text-xs text-destructive">{{ $message }}</p>@enderror

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar solução
      </button>
      <a href="{{ route('solucoes.index') }}" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
