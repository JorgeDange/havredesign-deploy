@extends('admin.layout')

@section('title', 'Editar Testemunho - Administração HAVREDESIGN')
@section('titulo', 'Editar testemunho')
@section('subtitulo', $testemunho->name)

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm p-6">
  <form method="POST" action="{{ route('testemunhos.update', $testemunho) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-foreground">Nome *</label>
        <input id="name" name="name" type="text" required maxlength="150"
               value="{{ old('name', $testemunho->name) }}"
               placeholder="Nome de quem fez o testemunho"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('name')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="role" class="block text-sm font-medium text-foreground">Cargo / função</label>
        <input id="role" name="role" type="text" maxlength="150"
               value="{{ old('role', $testemunho->role) }}"
               placeholder="Ex.: Cliente, Empreiteiro"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('role')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="space-y-2">
      <label for="content" class="block text-sm font-medium text-foreground">Testemunho *</label>
      <textarea id="content" name="content" rows="6" required
                placeholder="Texto apresentado no site."
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('content', $testemunho->content) }}</textarea>
      @error('content')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="space-y-2">
        <label for="status" class="block text-sm font-medium text-foreground">Estado *</label>
        <select id="status" name="status" required
                class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
          <option value="published" @selected(old('status', $testemunho->status) === 'published')>Publicado</option>
          <option value="hidden" @selected(old('status', $testemunho->status) === 'hidden')>Oculto</option>
        </select>
        @error('status')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="space-y-2">
        <label for="sort_order" class="block text-sm font-medium text-foreground">Ordem</label>
        <input id="sort_order" name="sort_order" type="number" min="0" step="1"
               value="{{ old('sort_order', $testemunho->sort_order) }}"
               class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
        @error('sort_order')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="flex flex-wrap gap-3 border-t border-border pt-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
        Guardar alterações
      </button>
      <a href="{{ route('testemunhos.index') }}" class="px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Voltar à lista
      </a>
    </div>
  </form>
</div>
@endsection
