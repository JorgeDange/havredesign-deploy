@extends('layouts.app')

@section('title', 'Verificar email - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Verifique o seu email</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">
      Obrigado por se registar! Antes de começar, verifique o seu email clicando no link que acabámos de enviar.
      Se não recebeu o email, podemos reenviar.
    </p>

    @if (session('status') == 'verification-link-sent')
      <div class="mb-4 rounded-md bg-primary/10 border border-primary/20 px-4 py-3 text-sm font-medium text-primary">
        Foi enviada uma nova ligação de verificação para o email indicado no registo.
      </div>
    @endif

    <div class="flex items-center justify-between mt-4">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Reenviar email
        </button>
      </form>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-foreground/70 underline hover:text-foreground">
          Terminar sessão
        </button>
      </form>
    </div>
  </div>
</section>
@endsection
