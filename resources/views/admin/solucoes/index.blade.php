@extends('admin.layout')

@section('title', 'Soluções - Administração HAVREDESIGN')
@section('titulo', 'Soluções')
@section('subtitulo', 'Gestão das soluções comerciais apresentadas no site.')

@section('admin-content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground">{{ $solucoes->count() }} solução(ões) registada(s)</p>
  <a href="{{ route('solucoes.create') }}" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Nova solução
  </a>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Código</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Descrição</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      @forelse ($solucoes as $solucao)
        <tr class="hover:bg-muted/50 transition-colors">
          <td class="px-4 py-3">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
              {{ $solucao->code }}
            </span>
          </td>
          <td class="px-4 py-3">
            <p class="font-medium text-foreground">{{ $solucao->name }}</p>
            <p class="text-xs text-muted-foreground">{{ $solucao->slug }}</p>
          </td>
          <td class="px-4 py-3 text-muted-foreground max-w-xs">
            {{ \Illuminate\Support\Str::limit($solucao->description, 90) }}
          </td>
          <td class="px-4 py-3 text-muted-foreground">{{ $solucao->sort_order }}</td>
          <td class="px-4 py-3">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $solucao->active ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ $solucao->active ? 'Ativa' : 'Inativa' }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
              <a href="{{ route('solucoes.edit', $solucao) }}"
                 class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                Editar
              </a>
              <form method="POST" action="{{ route('solucoes.destroy', $solucao) }}" class="inline"
                    onsubmit="return confirm('Apagar a solução «{{ $solucao->name }}»? Esta ação não pode ser desfeita.');">
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
          <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
            Ainda não existem soluções. Use «Nova solução» para criar a primeira.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
