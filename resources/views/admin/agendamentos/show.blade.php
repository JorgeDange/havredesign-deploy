@extends('admin.layout')

@section('title', 'Agendamento - Administração HAVREDESIGN')
@section('titulo', 'Agendamento')
@section('subtitulo', $agendamento->appt_date->format('d/m/Y') . ' às ' . substr((string) $agendamento->appt_time, 0, 5) . ' — ' . ($tipos[$agendamento->type]['label'] ?? $agendamento->type) . '.')

@section('admin-content')
@php
  $badges = [
    'PENDING' => 'bg-primary/10 text-primary',
    'CONFIRMED' => 'bg-primary/10 text-primary',
    'CANCELLED' => 'bg-destructive/10 text-destructive',
  ];
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="{{ route('agendamentos.index') }}" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$agendamento->status] ?? 'bg-muted text-muted-foreground' }}">
    {{ $estados[$agendamento->status] ?? $agendamento->status }}
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Marcação</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Data</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->appt_date->format('d/m/Y') }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Hora</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ substr((string) $agendamento->appt_time, 0, 5) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Tipo</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $tipos[$agendamento->type]['label'] ?? $agendamento->type }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Fuso horário</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->timezone ?: '—' }}</p>
        </div>

        @if ($agendamento->type === 'SITE')
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Morada</p>
            <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->address ?: '—' }}</p>
          </div>
        @endif

        @if ($agendamento->meeting_link)
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Ligação (reunião online)</p>
            <a href="{{ $agendamento->meeting_link }}" target="_blank" rel="noopener noreferrer"
               class="text-sm font-medium text-secondary hover:underline break-all">{{ $agendamento->meeting_link }}</a>
          </div>
        @endif

        <div class="sm:col-span-2">
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Notas do pedido</p>
          <p class="text-sm text-foreground/80 mt-1 whitespace-pre-wrap">{{ $agendamento->notes ?: '—' }}</p>
        </div>

        @if ($agendamento->fee_note)
          <div class="sm:col-span-2">
            <p class="text-xs uppercase tracking-wide text-muted-foreground">Nota/taxa registada</p>
            <p class="text-sm text-foreground/80 mt-1 whitespace-pre-wrap">{{ $agendamento->fee_note }}</p>
          </div>
        @endif
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Cliente</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Nome</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->user_name ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->user_email ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->user_phone ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Conta no site</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $agendamento->user?->name ?: 'Sem conta associada' }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Atualizar agendamento</h2>
      <p class="text-xs text-muted-foreground mb-4">
        Ao mudar o estado, o cliente recebe automaticamente um e-mail de aviso.
      </p>

      <form method="POST" action="{{ route('agendamentos.update', $agendamento) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
          <select id="status" name="status" required
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
            @foreach ($estados as $codigo => $rotulo)
              <option value="{{ $codigo }}" @selected(old('status', $agendamento->status) === $codigo)>{{ $rotulo }}</option>
            @endforeach
          </select>
          @error('status')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-2">
          <label for="fee_note" class="block text-sm font-medium text-foreground">Nota/taxa</label>
          <textarea id="fee_note" name="fee_note" rows="4" maxlength="500"
                    placeholder="Ex.: taxa de deslocação, condições acordadas..."
                    class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">{{ old('fee_note', $agendamento->fee_note) }}</textarea>
          <p class="text-xs text-muted-foreground">Enviada ao cliente no e-mail de estado, quando existir.</p>
          @error('fee_note')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full px-6 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
          Guardar alterações
        </button>
      </form>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Registo</h2>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Pedido em</dt>
          <dd class="text-foreground font-medium text-right">{{ $agendamento->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Confirmado em</dt>
          <dd class="text-foreground font-medium text-right">{{ $agendamento->confirmed_at?->format('d/m/Y H:i') ?: '—' }}</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Cancelado em</dt>
          <dd class="text-foreground font-medium text-right">{{ $agendamento->cancelled_at?->format('d/m/Y H:i') ?: '—' }}</dd>
        </div>
      </dl>
    </div>
  </div>
</div>
@endsection
