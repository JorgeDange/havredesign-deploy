@extends('admin.layout')

@section('title', 'Testemunhos - Administração HAVREDESIGN')
@section('titulo', 'Testemunhos')
@section('subtitulo', 'Aprovação e ordenação dos testemunhos apresentados no site.')

@section('admin-content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground">{{ $testemunhos->count() }} testemunho(s) registado(s)</p>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Testemunho</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      @forelse ($testemunhos as $testemunho)
        <tr class="hover:bg-muted/50 transition-colors">
          <td class="px-4 py-3">
            <p class="font-medium text-foreground">{{ $testemunho->name }}</p>
            @if ($testemunho->role)
              <p class="text-xs text-muted-foreground">{{ $testemunho->role }}</p>
            @endif
          </td>
          <td class="px-4 py-3 text-muted-foreground max-w-md">
            {{ \Illuminate\Support\Str::limit($testemunho->content, 120) }}
          </td>
          <td class="px-4 py-3 text-muted-foreground">{{ $testemunho->sort_order }}</td>
          <td class="px-4 py-3">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $testemunho->status === 'published' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ $testemunho->status === 'published' ? 'Publicado' : 'Oculto' }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
              <form method="POST" action="{{ route('testemunhos.toggle', $testemunho) }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Alternar
                </button>
              </form>
              <a href="{{ route('testemunhos.edit', $testemunho) }}"
                 class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                Editar
              </a>
              <form method="POST" action="{{ route('testemunhos.destroy', $testemunho) }}" class="inline"
                    onsubmit="return confirm('Apagar o testemunho de «{{ $testemunho->name }}»? Esta ação não pode ser desfeita.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-block px-3 py-1.5 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                  Apagar
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-foreground">
            Ainda não existem testemunhos.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
