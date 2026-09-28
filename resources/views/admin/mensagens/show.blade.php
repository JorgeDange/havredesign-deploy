@extends('admin.layout')

@section('title', 'Mensagem de contacto - Administração HAVREDESIGN')
@section('titulo', 'Mensagem de contacto')
@section('subtitulo', 'Recebida em ' . $mensagem->created_at->format('d/m/Y H:i') . '.')

@section('admin-content')
@php
  $badges = [
    'new' => 'bg-primary/10 text-primary',
    'replied' => 'bg-muted text-muted-foreground',
    'closed' => 'bg-muted text-muted-foreground',
  ];
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="{{ route('mensagens.index') }}" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$mensagem->status] ?? 'bg-muted text-muted-foreground' }}">
    {{ $estados[$mensagem->status] ?? $mensagem->status }}
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <div class="flex flex-wrap items-start justify-between gap-3 border-b border-border pb-4">
        <div>
          <h2 class="text-lg font-semibold text-foreground">{{ $mensagem->subject ?: 'Sem assunto' }}</h2>
          <p class="text-sm text-muted-foreground mt-1">
            {{ $mensagem->name }} · {{ $mensagem->created_at->format('d/m/Y H:i') }}
          </p>
        </div>
        <a href="mailto:{{ $mensagem->email }}" class="text-sm font-medium text-secondary hover:underline">
          Responder por e-mail
        </a>
      </div>

      <p class="text-sm text-foreground/80 mt-4 whitespace-pre-wrap">{{ $mensagem->message }}</p>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Remetente</h2>
      <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Nome</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5">{{ $mensagem->name }}</dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5">{{ $mensagem->email }}</dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5">{{ $mensagem->phone ?: '—' }}</dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">IP</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5">{{ $mensagem->ip_address ?: '—' }}</dd>
        </div>
        <div>
          <dt class="text-xs uppercase tracking-wide text-muted-foreground">Consentimento RGPD</dt>
          <dd class="text-sm font-medium text-foreground mt-0.5">{{ $mensagem->privacy_consented_at?->format('d/m/Y H:i') ?: '—' }}</dd>
        </div>
      </dl>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Estado da mensagem</h2>
      <p class="text-xs text-muted-foreground mb-4">Marque como respondida depois de responder ao cliente.</p>

      <form method="POST" action="{{ route('mensagens.update', $mensagem) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
          <select id="status" name="status" required
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
            @foreach ($estados as $codigo => $rotulo)
              <option value="{{ $codigo }}" @selected(old('status', $mensagem->status) === $codigo)>{{ $rotulo }}</option>
            @endforeach
          </select>
          @error('status')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="w-full px-6 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
          Guardar estado
        </button>
      </form>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Apagar mensagem</h2>
      <p class="text-xs text-muted-foreground mb-4">Esta ação não pode ser desfeita.</p>

      <form method="POST" action="{{ route('mensagens.destroy', $mensagem) }}"
            onsubmit="return confirm('Apagar esta mensagem? Esta ação não pode ser desfeita.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="w-full px-6 py-2.5 border border-destructive/40 text-destructive text-sm font-medium rounded-md hover:bg-destructive/10 transition-colors">
          Apagar mensagem
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
