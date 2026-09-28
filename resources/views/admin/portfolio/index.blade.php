@extends('admin.layout')

@section('title', 'Portefólio - Administração HAVREDESIGN')
@section('titulo', 'Portefólio')
@section('subtitulo', $projetos->count() . ' projeto(s) no portefólio')

@section('admin-content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <p class="text-sm text-muted-foreground">Organize os projetos por ordem, estado de publicação e destaque no Início.</p>
  <a href="{{ route('portfolio.create') }}" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground text-sm font-medium hover:bg-secondary/90 transition-colors">
    Novo projeto
  </a>
</div>

<div class="bg-card border border-border rounded-xl shadow-sm mt-4 overflow-hidden">
  @if ($projetos->isEmpty())
    <div class="p-8 text-center">
      <p class="text-sm text-muted-foreground">Ainda não existem projetos no portefólio.</p>
      <a href="{{ route('portfolio.create') }}" class="inline-block mt-4 px-4 py-2 rounded-md border border-border text-foreground text-sm font-medium hover:bg-muted transition-colors">
        Criar o primeiro projeto
      </a>
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm min-w-[760px]">
        <thead>
          <tr class="bg-muted text-muted-foreground">
            <th scope="col" class="text-left font-medium px-4 py-3">Imagem</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Título</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Categoria</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Estado</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Destaque</th>
            <th scope="col" class="text-left font-medium px-4 py-3">Ordem</th>
            <th scope="col" class="text-right font-medium px-4 py-3">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          @foreach ($projetos as $projeto)
            <tr>
              <td class="px-4 py-3">
                <img src="{{ \App\Support\Media::thumbnail($projeto->image_url) }}"
                     alt="{{ $projeto->title }}"
                     class="h-12 w-16 rounded-md object-cover bg-muted border border-border" />
              </td>
              <td class="px-4 py-3">
                <span class="font-medium text-foreground">{{ $projeto->title }}</span>
                @if ($projeto->slug)
                  <span class="block text-xs text-muted-foreground">/portfolio/{{ $projeto->slug }}</span>
                @endif
              </td>
              <td class="px-4 py-3 text-muted-foreground">{{ $projeto->category }}</td>
              <td class="px-4 py-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $projeto->status === 'published' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                  {{ $projeto->status === 'published' ? 'Publicado' : 'Rascunho' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $projeto->featured ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                  {{ $projeto->featured ? 'Sim' : 'Não' }}
                </span>
              </td>
              <td class="px-4 py-3 text-muted-foreground">{{ $projeto->sort_order }}</td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="{{ route('portfolio.edit', $projeto) }}"
                   class="inline-block px-3 py-1.5 rounded-md border border-border text-foreground text-xs font-medium hover:bg-muted transition-colors">
                  Editar
                </a>
                <form method="post" action="{{ route('portfolio.destroy', $projeto) }}" class="inline"
                      onsubmit="return confirm('Apagar o projeto «{{ $projeto->title }}»? Será removido do portefólio e não pode ser anulado.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="inline-block px-3 py-1.5 rounded-md border border-destructive/30 text-destructive text-xs font-medium hover:bg-destructive/10 transition-colors">
                    Apagar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
