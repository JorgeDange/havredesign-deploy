@extends('layouts.app')

@section('title', 'Minha conta - HAVREDESIGN')

@section('content')
<section class="max-w-5xl mx-auto px-4 py-12">
  <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl sm:text-4xl font-bold text-foreground">Minha conta</h1>
      <p class="text-foreground/70 mt-2">Olá, {{ $user->name }} — acompanhe aqui os seus pedidos e agendamentos.</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="px-5 py-2.5 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
        Terminar sessão
      </button>
    </form>
  </div>

  <div class="bg-card border border-border rounded-xl shadow-sm p-6 mb-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-sm text-muted-foreground">Email</p>
        <p class="font-medium text-foreground">{{ $user->email }}</p>
        <p class="text-sm text-muted-foreground mt-2">Membro desde {{ $user->created_at?->format('d/m/Y') ?? '-' }}</p>
      </div>
      @if ($user->isAdmin())
        <a href="{{ route('admin') }}" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors">
          Painel administrativo
        </a>
      @endif
    </div>
  </div>

  <div class="grid gap-6 md:grid-cols-2">
    {{-- Pedidos de orçamento --}}
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Pedidos de orçamento</h2>

      @if ($pedidos->isEmpty())
        <p class="text-sm text-muted-foreground">Ainda não fez pedidos de orçamento.</p>
        <a href="{{ route('solicitar-projeto') }}" class="inline-block mt-3 text-sm font-medium text-secondary hover:underline">
          Solicitar orçamento →
        </a>
      @else
        <ul class="divide-y divide-border">
          @foreach ($pedidos as $pedido)
            <li class="py-3 first:pt-0 last:pb-0">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-foreground">{{ $pedido->project_type }}</p>
                  <p class="text-sm text-muted-foreground">
                    {{ $pedido->created_at->format('d/m/Y') }}
                    @if ($pedido->location) · {{ $pedido->location }} @endif
                    @if ($pedido->budget) · {{ $pedido->budget }} @endif
                  </p>
                </div>
                <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full {{ $pedido->status === 'APPROVED' || $pedido->status === 'COMPLETED' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
                  {{ ['NEW' => 'Recebido', 'IN_REVIEW' => 'Em análise', 'APPROVED' => 'Aprovado', 'IN_PROGRESS' => 'Em curso', 'COMPLETED' => 'Concluído', 'REJECTED' => 'Recusado'][$pedido->status] ?? $pedido->status }}
                </span>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    {{-- Agendamentos --}}
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Agendamentos</h2>

      @if ($agendamentos->isEmpty())
        <p class="text-sm text-muted-foreground">Ainda não tem agendamentos.</p>
        <a href="{{ route('agendar') }}" class="inline-block mt-3 text-sm font-medium text-secondary hover:underline">
          Marcar reunião →
        </a>
      @else
        <ul class="divide-y divide-border">
          @foreach ($agendamentos as $agendamento)
            <li class="py-3 first:pt-0 last:pb-0">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="font-medium text-foreground">
                    {{ $agendamento->appt_date->format('d/m/Y') }} · {{ substr((string) $agendamento->appt_time, 0, 5) }}
                  </p>
                  <p class="text-sm text-muted-foreground">
                    {{ $tipos[$agendamento->type]['label'] ?? $agendamento->type }}
                    @if ($agendamento->address) · {{ $agendamento->address }} @endif
                  </p>
                </div>
                <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full {{ $agendamento->status === 'CONFIRMED' ? 'bg-primary/10 text-primary' : ($agendamento->status === 'CANCELLED' ? 'bg-destructive/10 text-destructive' : 'bg-muted text-muted-foreground') }}">
                  {{ ['PENDING' => 'Por confirmar', 'CONFIRMED' => 'Confirmado', 'CANCELLED' => 'Cancelado'][$agendamento->status] ?? $agendamento->status }}
                </span>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</section>
@endsection
