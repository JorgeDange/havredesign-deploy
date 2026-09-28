@extends('admin.layout')

@section('title', 'Pedido de orçamento - Administração HAVREDESIGN')
@section('titulo', 'Pedido de orçamento')
@section('subtitulo', 'Ref. ' . strtoupper(substr((string) $pedido->id, 0, 8)) . ' — submetido em ' . $pedido->created_at->format('d/m/Y H:i') . '.')

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
  $canais = ['email' => 'E-mail', 'phone' => 'Telefone', 'whatsapp' => 'WhatsApp'];
  $tamanho = function (int $bytes): string {
      if ($bytes >= 1048576) {
          return number_format($bytes / 1048576, 2, ',', '.').' MB';
      }
      if ($bytes >= 1024) {
          return number_format($bytes / 1024, 0, ',', '.').' KB';
      }
      return $bytes.' B';
  };
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="{{ route('pedidos.index') }}" class="text-sm font-medium text-secondary hover:underline">← Voltar à lista</a>
  <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $badges[$pedido->status] ?? 'bg-muted text-muted-foreground' }}">
    {{ $estados[$pedido->status] ?? $pedido->status }}
  </span>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-4">
  <div class="lg:col-span-2 space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Cliente e contactos</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Nome</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->user_name ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">E-mail</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->user_email ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Telefone</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->user_phone ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Conta no site</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->user?->name ?: 'Sem conta associada' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Canal preferido</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $canais[$pedido->preferred_channel] ?? ($pedido->preferred_channel ?: '—') }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Horário preferido</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->preferred_time ?: '—' }}</p>
        </div>
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-4">Projeto</h2>
      <div class="grid sm:grid-cols-2 gap-x-6 gap-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Tipo de projeto</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->project_type ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Localização</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->location ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Área aproximada</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->area_approx ? $pedido->area_approx.' m²' : '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Estado do projeto</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->project_stage ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Serviço pretendido</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->service?->title ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">HAVRE Solução pretendida</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->solution?->name ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Segmento</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->segment ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Orçamento previsto</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->budget ?: '—' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-muted-foreground">Prazo desejado</p>
          <p class="text-sm font-medium text-foreground mt-0.5">{{ $pedido->timeline ?: '—' }}</p>
        </div>
      </div>

      <div class="mt-5 pt-5 border-t border-border">
        <p class="text-xs uppercase tracking-wide text-muted-foreground">Descrição do projeto</p>
        <p class="text-sm text-foreground/80 mt-2 whitespace-pre-wrap">{{ $pedido->description ?: '—' }}</p>
      </div>
    </div>

    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-foreground">Anexos</h2>
        <span class="text-xs text-muted-foreground">{{ $anejos->count() }} ficheiro(s)</span>
      </div>
      @if ($anejos->isEmpty())
        <p class="text-sm text-muted-foreground">Este pedido não tem anexos.</p>
      @else
        <ul class="divide-y divide-border">
          @foreach ($anejos as $anejo)
            <li class="py-3 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-3">
              <div class="min-w-0 flex items-center gap-3">
                <span class="shrink-0 w-9 h-9 rounded-md bg-muted border border-border flex items-center justify-center text-muted-foreground" aria-hidden="true">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 1113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </span>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-foreground truncate">{{ $anejo->original_name }}</p>
                  <p class="text-xs text-muted-foreground">{{ $anejo->mime_type }} · {{ $tamanho((int) $anejo->size_bytes) }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                @if ($anejo->visualizavel())
                  <button type="button"
                          data-anexo-ver="{{ route('pedidos.anexo.preview', [$pedido, $anejo]) }}"
                          data-anexo-baixar="{{ route('pedidos.anexo.download', [$pedido, $anejo]) }}"
                          data-anexo-nome="{{ $anejo->original_name }}"
                          data-anexo-tipo="{{ $anejo->ehImagem() ? 'imagem' : 'documento' }}"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-border bg-card text-xs font-medium text-foreground hover:bg-muted transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Ver
                  </button>
                @endif
                <a href="{{ route('pedidos.anexo.download', [$pedido, $anejo]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary text-secondary-foreground text-xs font-medium hover:bg-secondary/90 transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                  Baixar
                </a>
              </div>
            </li>
          @endforeach
        </ul>
        <p class="text-xs text-muted-foreground mt-4">Ficheiros privados: só são servidos através do painel, com verificação de sessão de administrador.</p>
      @endif
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-card border border-border rounded-xl shadow-sm p-6">
      <h2 class="text-lg font-semibold text-foreground mb-1">Estado do pedido</h2>
      <p class="text-xs text-muted-foreground mb-4">Ao alterar o estado, o pedido passa a aparecer filtrado nessa aba.</p>

      <form method="POST" action="{{ route('pedidos.update', $pedido) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium text-foreground">Estado</label>
          <select id="status" name="status" required
                  class="w-full px-4 py-2.5 rounded-md border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary">
            @foreach ($estados as $codigo => $rotulo)
              <option value="{{ $codigo }}" @selected(old('status', $pedido->status) === $codigo)>{{ $rotulo }}</option>
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
      <h2 class="text-lg font-semibold text-foreground mb-4">Registo</h2>
      <dl class="space-y-3 text-sm">
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Recebido</dt>
          <dd class="text-foreground font-medium text-right">{{ $pedido->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Consentimento RGPD</dt>
          <dd class="text-foreground font-medium text-right">{{ $pedido->privacy_consented_at?->format('d/m/Y H:i') ?: '—' }}</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">Versão da política</dt>
          <dd class="text-foreground font-medium text-right">{{ $pedido->privacy_version ?: '—' }}</dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-muted-foreground">IP</dt>
          <dd class="text-foreground font-medium text-right">{{ $pedido->ip_address ?: '—' }}</dd>
        </div>
      </dl>
    </div>
  </div>
</div>

<!-- Modal de visualização de anexos (usa as rotas existentes, sem rotas novas) -->
<div id="anexoModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="anexoModalTitulo">
  <div class="absolute inset-0 bg-black/70" data-anexo-fechar></div>
  <div class="relative h-full flex items-center justify-center p-3 md:p-6">
    <div class="flex h-[85vh] w-full max-w-5xl flex-col overflow-hidden rounded-xl border border-border bg-card shadow-2xl">
      <div class="flex items-start justify-between gap-4 border-b border-border px-4 py-3">
        <div class="min-w-0">
          <p id="anexoModalTitulo" class="text-sm font-medium text-foreground truncate">Anexo</p>
          <p class="text-xs text-muted-foreground mt-0.5">Pré-visualização — o ficheiro continua privado.</p>
        </div>
        <button type="button" data-anexo-fechar aria-label="Fechar pré-visualização"
                class="shrink-0 rounded-md p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <div class="flex-1 min-h-0 bg-muted p-3 flex items-center justify-center">
        <img id="anexoModalImg" src="" alt="" class="hidden max-h-full max-w-full rounded-md bg-white object-contain" />
        <iframe id="anexoModalFrame" src="" title="Pré-visualização do anexo" class="hidden h-full w-full rounded-md border-0 bg-white"></iframe>
        <p id="anexoModalVazio" class="hidden text-sm text-muted-foreground">Não é possível pré-visualizar este formato — use «Baixar».</p>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-4 py-3">
        <p id="anexoModalMeta" class="text-xs text-muted-foreground truncate"></p>
        <a id="anexoModalBaixar" href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-secondary text-secondary-foreground text-xs font-medium hover:bg-secondary/90 transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
          Baixar
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('anexoModal');
    if (!modal) return;

    const img = document.getElementById('anexoModalImg');
    const frame = document.getElementById('anexoModalFrame');
    const vazio = document.getElementById('anexoModalVazio');
    const titulo = document.getElementById('anexoModalTitulo');
    const meta = document.getElementById('anexoModalMeta');
    const baixar = document.getElementById('anexoModalBaixar');
    let gatilho = null;

    function abrir(botao) {
      gatilho = botao;
      titulo.textContent = botao.dataset.anexoNome || 'Anexo';
      meta.textContent = botao.dataset.anexoNome || '';
      baixar.href = botao.dataset.anexoBaixar || '#';

      const url = botao.dataset.anexoVer;
      const imagem = botao.dataset.anexoTipo === 'imagem';

      img.classList.add('hidden');
      frame.classList.add('hidden');
      vazio.classList.add('hidden');

      if (imagem) {
        img.src = url;
        img.classList.remove('hidden');
      } else {
        frame.src = url;
        frame.classList.remove('hidden');
      }

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      const fechar = modal.querySelector('[data-anexo-fechar]');
      if (fechar) fechar.focus();
    }

    function fechar() {
      modal.classList.add('hidden');
      img.src = '';
      frame.src = '';
      document.body.style.overflow = '';
      if (gatilho) gatilho.focus();
    }

    document.querySelectorAll('[data-anexo-ver]').forEach((botao) => {
      botao.addEventListener('click', () => abrir(botao));
    });

    modal.querySelectorAll('[data-anexo-fechar]').forEach((alvo) => {
      alvo.addEventListener('click', fechar);
    });

    document.addEventListener('keydown', (evento) => {
      if (evento.key === 'Escape' && !modal.classList.contains('hidden')) fechar();
    });
  });
</script>
@endpush
