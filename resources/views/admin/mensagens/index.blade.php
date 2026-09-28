@extends('admin.layout')

@section('title', 'Mensagens de contacto - Administração HAVREDESIGN')
@section('titulo', 'Mensagens de contacto')
@section('subtitulo', 'Mensagens enviadas pelo formulário de contacto do site.')

@section('admin-content')
@php
  $badges = [
    'new' => 'bg-primary/10 text-primary',
    'replied' => 'bg-muted text-muted-foreground',
    'closed' => 'bg-muted text-muted-foreground',
  ];
@endphp

<div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
  <div class="border-b border-border px-4 py-3 flex flex-wrap items-center gap-2">
    <a href="{{ route('mensagens.index') }}"
       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors {{ $status === '' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground hover:bg-muted' }}">
      Todas
      <span class="px-1.5 py-0.5 rounded-full {{ $status === '' ? 'bg-primary/15 text-primary' : 'bg-card text-muted-foreground' }}">{{ $contagens['total'] }}</span>
    </a>

    @foreach ($estados as $codigo => $rotulo)
      <a href="{{ route('mensagens.index', ['status' => $codigo]) }}"
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
          <th class="px-4 py-3 font-medium text-muted-foreground">Nome</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">E-mail</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Assunto</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Mensagem</th>
          <th class="px-4 py-3 font-medium text-muted-foreground">Estado</th>
          <th class="px-4 py-3 font-medium text-muted-foreground text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border">
        @forelse ($mensagens as $mensagem)
          <tr class="hover:bg-muted/50 transition-colors">
            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $mensagem->created_at->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-3 font-medium text-foreground">{{ $mensagem->name }}</td>
            <td class="px-4 py-3 text-foreground/80">{{ $mensagem->email }}</td>
            <td class="px-4 py-3 text-foreground/80">{{ $mensagem->subject ?: '—' }}</td>
            <td class="px-4 py-3 text-muted-foreground max-w-xs">
              <span class="block truncate">{{ \Illuminate\Support\Str::limit($mensagem->message, 70) }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$mensagem->status] ?? 'bg-muted text-muted-foreground' }}">
                {{ $estados[$mensagem->status] ?? $mensagem->status }}
              </span>
            </td>
            <td class="px-4 py-3 text-right whitespace-nowrap">
              <a href="{{ route('mensagens.show', $mensagem) }}" class="text-sm font-medium text-secondary hover:underline">Ver mensagem</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-foreground">
              Sem mensagens{{ $status !== '' ? ' com o estado selecionado' : '' }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if ($mensagens->hasPages())
  <div class="mt-4">{!! $mensagens->links() !!}</div>
@endif
@endsection
