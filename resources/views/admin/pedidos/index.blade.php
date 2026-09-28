@extends('admin.layout')

@section('title', 'Pedidos de orçamento - Administração HAVREDESIGN')
@section('titulo', 'Pedidos de orçamento')
@section('subtitulo', 'Pedidos de orçamento recebidos pelo formulário do site.')

@section('admin-content')
@php
  $badges = [
    'NEW' => 'bg-primary/10 text-primary',
    'IN_REVIEW' => 'bg-muted text-muted-foreground',
    'APPROVED' => 'bg-primary/10 text-primary',
    'IN_PROGRESS' => 'bg-muted text-muted-foreground',
    'COMPLETED' => 'bg-primary/10 text-primary',
    'REJECTED' => 'bg-destructive/10 text-destructive',
  ];
@endphp

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center gap-2">
    <a href="{{ route('pedidos.index') }}"
       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' }}">
      Todos
      <span class="px-1.5 py-0.5 rounded-full {{ $status === '' ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' }}">{{ $contagens['total'] }}</span>
    </a>

    @foreach ($estados as $codigo => $rotulo)
      <a href="{{ route('pedidos.index', ['status' => $codigo]) }}"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $status === $codigo ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' }}">
        {{ $rotulo }}
        <span class="px-1.5 py-0.5 rounded-full {{ $status === $codigo ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' }}">{{ $contagens[$codigo] }}</span>
      </a>
    @endforeach
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-muted text-left">
          <th class="px-4 py-3 font-medium text-muted-foreground">Data</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Cliente</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Tipo de projeto</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Localização</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        @forelse ($pedidos as $pedido)
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-3">
              <p class="font-medium text-foreground">{{ $pedido->user_name }}</p>
              <p class="text-xs text-muted-foreground">{{ $pedido->user_email }}</p>
            </td>
            <td class="px-4 py-3 text-foreground/80">{{ $pedido->project_type }}</td>
            <td class="px-4 py-3 text-muted-foreground">{{ $pedido->location ?: '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$pedido->status] ?? 'bg-muted text-muted-foreground' }}">
                {{ $estados[$pedido->status] ?? $pedido->status }}
              </span>
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a href="{{ route('pedidos.show', $pedido) }}" class="text-sm font-medium text-secondary hover:underline">Ver detalhe</a>
              @if ((int) $pedido->attachments_count > 0)
                <span class="ml-2 text-xs text-muted-foreground">{{ $pedido->attachments_count }} anexo(s)</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem pedidos{{ $status !== '' ? ' com o estado selecionado' : '' }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if ($pedidos->hasPages())
  <div class="mt-4">{!! $pedidos->links() !!}</div>
@endif
@endsection
