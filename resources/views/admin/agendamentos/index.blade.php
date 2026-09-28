@extends('admin.layout')

@section('title', 'Agendamentos - Administração HAVREDESIGN')
@section('titulo', 'Agendamentos')
@section('subtitulo', 'Reuniões e visitas pedidas pelo site.')

@section('admin-content')
@php
  $badges = [
    'PENDING' => 'bg-primary/10 text-primary',
    'CONFIRMED' => 'bg-primary/10 text-primary',
    'CANCELLED' => 'bg-destructive/10 text-destructive',
  ];
@endphp

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-2">
      <a href="{{ route('agendamentos.index', array_filter(['data' => $data])) }}"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' }}">
        Todos
        <span class="px-1.5 py-0.5 rounded-full {{ $status === '' ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' }}">{{ $contagens['total'] }}</span>
      </a>

      @foreach ($estados as $codigo => $rotulo)
        <a href="{{ route('agendamentos.index', array_filter(['status' => $codigo, 'data' => $data])) }}"
           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $status === $codigo ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' }}">
          {{ $rotulo }}
          <span class="px-1.5 py-0.5 rounded-full {{ $status === $codigo ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' }}">{{ $contagens[$codigo] }}</span>
        </a>
      @endforeach
    </div>

    <form method="GET" action="{{ route('agendamentos.index') }}" class="flex flex-wrap items-end gap-2">
      @if ($status !== '')
        <input type="hidden" name="status" value="{{ $status }}" />
      @endif

      <div class="space-y-1">
        <label for="data" class="block text-xs text-muted-foreground">Data</label>
        <input id="data" name="data" type="date" value="{{ $data }}"
               class="px-3 py-1.5 rounded-md border border-border bg-card text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-secondary" />
      </div>

      <button type="submit" class="px-3 py-1.5 border border-border bg-card text-foreground text-sm font-medium rounded-md hover:bg-muted transition-colors">
        Filtrar
      </button>

      @if ($data !== '')
        <a href="{{ route('agendamentos.index', array_filter(['status' => $status])) }}" class="px-3 py-1.5 text-xs font-medium text-secondary hover:underline">
          Limpar data
        </a>
      @endif
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-muted text-left">
          <th class="px-4 py-3 font-medium text-muted-foreground">Data</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Hora</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Cliente</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Tipo</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        @forelse ($agendamentos as $agendamento)
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3 text-foreground whitespace-nowrap">{{ $agendamento->appt_date->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ substr((string) $agendamento->appt_time, 0, 5) }}</td>
            <td class="px-4 py-3">
              <p class="font-medium text-foreground">{{ $agendamento->user_name }}</p>
              <p class="text-xs text-muted-foreground">{{ $agendamento->user_email }}</p>
            </td>
            <td class="px-4 py-3 text-foreground/80">{{ $tipos[$agendamento->type]['label'] ?? $agendamento->type }}</td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$agendamento->status] ?? 'bg-muted text-muted-foreground' }}">
                {{ $estados[$agendamento->status] ?? $agendamento->status }}
              </span>
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a href="{{ route('agendamentos.show', $agendamento) }}" class="text-sm font-medium text-secondary hover:underline">Ver detalhe</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem agendamentos para os filtros selecionados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if ($agendamentos->hasPages())
  <div class="mt-4">{!! $agendamentos->links() !!}</div>
@endif
@endsection
