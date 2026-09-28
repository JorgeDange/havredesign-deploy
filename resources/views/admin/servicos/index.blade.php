@extends('admin.layout')

@section('title', 'Serviços - Administração HAVREDESIGN')
@section('titulo', 'Serviços')
@section('subtitulo', 'Gestão dos serviços principais e complementares apresentados no site.')

@section('admin-content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground">{{ $servicos->count() }} serviço(s) registado(s)</p>
  <a href="{{ route('servicos.create') }}" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Novo serviço
  </a>
</div>

<div class="mt-4 bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="bg-muted text-left">
        <th class="px-4 py-3 font-medium text-muted-foreground">Imagem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Serviço</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Grupo</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Ordem</th>
        <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
        <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      @forelse ($servicos as $servico)
        <tr class="hover:bg-muted/50 transition-colors">
          <td class="px-4 py-3">
            <img src="{{ \App\Support\Media::thumbnail($servico->image_url) }}"
                 alt="{{ $servico->title }}"
                 loading="lazy"
                 class="h-12 w-16 rounded-md object-cover border border-border bg-muted" />
          </td>
          <td class="px-4 py-3">
            <p class="font-medium text-foreground">{{ $servico->title }}</p>
            <p class="text-xs text-muted-foreground">{{ $servico->slug }}</p>
          </td>
          <td class="px-4 py-3">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $servico->group === 'principal' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ $servico->group === 'principal' ? 'Principal' : 'Complementar' }}
            </span>
          </td>
          <td class="px-4 py-3 text-muted-foreground">{{ $servico->sort_order }}</td>
          <td class="px-4 py-3">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $servico->active ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ $servico->active ? 'Ativo' : 'Inativo' }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
              <a href="{{ route('servicos.edit', $servico) }}"
                 class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                Editar
              </a>
              <form method="POST" action="{{ route('servicos.toggle', $servico) }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  {{ $servico->active ? 'Desativar' : 'Ativar' }}
                </button>
              </form>
              <form method="POST" action="{{ route('servicos.destroy', $servico) }}" class="inline"
                    onsubmit="return confirm('Apagar o serviço «{{ $servico->title }}»? Esta ação não pode ser desfeita.');">
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
            Ainda não existem serviços. Use «Novo serviço» para criar o primeiro.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
