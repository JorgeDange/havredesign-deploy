@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <p class="text-sm font-medium text-secondary tracking-wide uppercase">Administração</p>
      <h1 class="text-3xl font-bold text-foreground mt-1">@yield('titulo', 'Painel')</h1>
      @hasSection('subtitulo')
        <p class="text-foreground/70 mt-1">@yield('subtitulo')</p>
      @endif
    </div>
    <a href="{{ url('/') }}" class="px-4 py-2 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
      Ver site ↗
    </a>
  </div>

  @if (session('ok'))
    <div class="mt-6 rounded-md border border-primary/20 bg-primary/10 px-4 py-3 text-sm font-medium text-primary">
      {{ session('ok') }}
    </div>
  @endif

  @if (session('erro'))
    <div class="mt-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive">
      {{ session('erro') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mt-6 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive">
      <p class="font-medium">Corrija os seguintes campos:</p>
      <ul class="list-disc list-inside mt-1">
        @foreach ($errors->all() as $erro)
          <li>{{ $erro }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <nav class="mt-6 border-b border-border flex gap-1 overflow-x-auto" aria-label="Secções do painel">
    @php
      $abas = [
          ['admin', 'admin', 'Painel'],
          ['servicos.index', 'servicos.*', 'Serviços'],
          ['portfolio.index', 'portfolio.*', 'Portefólio'],
          ['solucoes.index', 'solucoes.*', 'Soluções'],
          ['pedidos.index', 'pedidos.*', 'Pedidos'],
          ['agendamentos.index', 'agendamentos.*', 'Agendamentos'],
          ['mensagens.index', 'mensagens.*', 'Mensagens'],
          ['testemunhos.index', 'testemunhos.*', 'Testemunhos'],
          ['definicoes.index', 'definicoes.*', 'Definições'],
          ['utilizadores.index', 'utilizadores.*', 'Utilizadores'],
      ];
    @endphp
    @foreach ($abas as [$rota, $padrao, $rotulo])
      <a href="{{ route($rota) }}"
         class="shrink-0 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors {{ request()->routeIs($padrao) ? 'border-secondary text-foreground' : 'border-transparent text-foreground/60 hover:text-foreground' }}">
        {{ $rotulo }}
      </a>
    @endforeach
  </nav>

  <div class="mt-8">
    @yield('admin-content')
  </div>
</div>
@endsection
