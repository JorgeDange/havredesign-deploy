@extends('admin.layout')

@section('title', 'Painel - Administração HAVREDESIGN')
@section('titulo', 'Painel')
@section('subtitulo', 'Resumo da operação — ' . now()->format('d/m/Y'))

@section('admin-content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
  @foreach ([
      ['Pedidos novos', $contagens['pedidosNovos'], route('pedidos.index') . '?status=NEW'],
      ['Agendamentos por confirmar', $contagens['agendamentosPendentes'], route('agendamentos.index') . '?status=PENDING'],
      ['Mensagens por ler', $contagens['mensagensNovas'], route('mensagens.index') . '?status=new'],
      ['Testemunhos ocultos', $contagens['testemunhosOcultos'], route('testemunhos.index')],
  ] as [$rotulo, $numero, $link])
    <a href="{{ $link }}" class="bg-card border border-border rounded-xl shadow-sm p-5 hover:border-secondary transition-colors">
      <p class="text-sm text-muted-foreground">{{ $rotulo }}</p>
      <p class="text-3xl font-bold text-foreground mt-1">{{ $numero }}</p>
    </a>
  @endforeach
</div>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-4">
  @foreach ([
      ['Serviços', $contagens['servicos'], route('servicos.index')],
      ['Projetos', $contagens['portfolio'], route('portfolio.index')],
      ['Soluções', $contagens['solucoes'], route('solucoes.index')],
      ['Utilizadores', $contagens['utilizadores'], route('utilizadores.index')],
  ] as [$rotulo, $numero, $link])
    <a href="{{ $link }}" class="bg-card border border-border rounded-xl shadow-sm p-5 hover:border-secondary transition-colors flex items-baseline justify-between">
      <span class="text-sm text-muted-foreground">{{ $rotulo }}</span>
      <span class="text-xl font-semibold text-foreground">{{ $numero }}</span>
    </a>
  @endforeach
</div>

<div class="grid gap-6 lg:grid-cols-2 mt-8">
  <div class="bg-card border border-border rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-foreground">Agendamentos próximos</h2>
      <a href="{{ route('agendamentos.index') }}" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    @if ($agendamentosProximos->isEmpty())
      <p class="text-sm text-muted-foreground">Sem agendamentos para já.</p>
    @else
      <ul class="divide-y divide-border">
        @foreach ($agendamentosProximos as $agendamento)
          <li class="py-3 first:pt-0 last:pb-0">
            <a href="{{ route('agendamentos.show', $agendamento) }}" class="flex items-center justify-between gap-3 hover:opacity-80">
              <span class="text-sm font-medium text-foreground">
                {{ $agendamento->appt_date->format('d/m/Y') }} · {{ substr((string) $agendamento->appt_time, 0, 5) }} · {{ $agendamento->user_name }}
              </span>
              <span class="text-xs font-medium px-2 py-1 rounded-full {{ $agendamento->status === 'CONFIRMED' ? 'bg-primary/10 text-primary' : ($agendamento->status === 'CANCELLED' ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground') }}">
                {{ ['PENDING' => 'Por confirmar', 'CONFIRMED' => 'Confirmado', 'CANCELLED' => 'Cancelado'][$agendamento->status] }}
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </div>

  <div class="bg-card border border-border rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-foreground">Últimos pedidos de orçamento</h2>
      <a href="{{ route('pedidos.index') }}" class="text-sm text-secondary hover:underline">Ver todos</a>
    </div>
    @if ($pedidosRecentes->isEmpty())
      <p class="text-sm text-muted-foreground">Ainda sem pedidos.</p>
    @else
      <ul class="divide-y divide-border">
        @foreach ($pedidosRecentes as $pedido)
          <li class="py-3 first:pt-0 last:pb-0">
            <a href="{{ route('pedidos.show', $pedido) }}" class="flex items-center justify-between gap-3 hover:opacity-80">
              <span class="text-sm font-medium text-foreground">
                {{ $pedido->project_type }} · {{ $pedido->user_name }}
              </span>
              <span class="text-xs font-medium px-2 py-1 rounded-full bg-muted text-muted-foreground">
                {{ ['NEW' => 'Novo', 'IN_REVIEW' => 'Em análise', 'APPROVED' => 'Aprovado', 'IN_PROGRESS' => 'Em curso', 'COMPLETED' => 'Concluído', 'REJECTED' => 'Recusado'][$pedido->status] ?? $pedido->status }}
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>

<div class="bg-card border border-border rounded-xl shadow-sm p-6 mt-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold text-foreground">Últimas mensagens de contacto</h2>
    <a href="{{ route('mensagens.index') }}" class="text-sm text-secondary hover:underline">Ver todas</a>
  </div>
  @if ($mensagensRecentes->isEmpty())
    <p class="text-sm text-muted-foreground">Ainda sem mensagens.</p>
  @else
    <ul class="divide-y divide-border">
      @foreach ($mensagensRecentes as $mensagem)
        <li class="py-3 first:pt-0 last:pb-0">
          <a href="{{ route('mensagens.show', $mensagem) }}" class="flex items-center justify-between gap-3 hover:opacity-80">
            <span class="text-sm font-medium text-foreground">
              {{ $mensagem->name }} — {{ \Illuminate\Support\Str::limit($mensagem->message, 60) }}
            </span>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $mensagem->status === 'new' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ ['new' => 'Nova', 'replied' => 'Respondida', 'closed' => 'Fechada'][$mensagem->status] }}
            </span>
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endsection
