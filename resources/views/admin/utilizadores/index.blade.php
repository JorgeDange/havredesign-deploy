@extends('admin.layout')

@section('title', 'Utilizadores - Administração HAVREDESIGN')
@section('titulo', 'Utilizadores')
@section('subtitulo', 'Contas registadas e papel de acesso ao painel (USER ou ADMIN).')

@section('admin-content')
<div class="bg-card border border-border rounded-xl shadow-sm overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead>
      <tr class="border-b border-border bg-muted/60">
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Nome</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">E-mail</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Telefone</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Papel</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">Registado</th>
        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-muted-foreground">Ação</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-border">
      @forelse ($utilizadores as $utilizador)
        <tr class="hover:bg-muted/40 transition-colors">
          <td class="px-6 py-4 text-foreground font-medium">{{ $utilizador->name }}</td>
          <td class="px-6 py-4 text-muted-foreground">{{ $utilizador->email }}</td>
          <td class="px-6 py-4 text-muted-foreground">{{ $utilizador->phone ?: '—' }}</td>
          <td class="px-6 py-4">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $utilizador->role === 'ADMIN' ? 'bg-primary/10 text-primary' : 'bg-muted text-muted-foreground' }}">
              {{ $utilizador->role }}
            </span>
          </td>
          <td class="px-6 py-4 text-muted-foreground">{{ $utilizador->created_at?->format('d/m/Y H:i') }}</td>
          <td class="px-6 py-4 text-right">
            @if ($utilizador->id === auth()->id())
              <span class="text-xs font-medium text-muted-foreground">Si próprio</span>
            @else
              <form method="POST" action="{{ route('utilizadores.papel', $utilizador) }}"
                    onsubmit="return confirm('Deseja alterar o papel deste utilizador?');">
                @csrf
                <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium border border-border rounded-md text-foreground hover:bg-muted transition-colors">
                  {{ $utilizador->role === 'ADMIN' ? 'Tornar USER' : 'Tornar ADMIN' }}
                </button>
              </form>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-sm text-muted-foreground">Sem utilizadores registados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
