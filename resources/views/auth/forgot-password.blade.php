@extends('layouts.app')

@section('title', 'Recuperar palavra-passe - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Recuperar palavra-passe</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">
      Esqueceu a palavra-passe? Informe o seu email e enviaremos um link para escolher uma nova.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nome@exemplo.ao" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="flex items-center justify-between mt-6">
        <a class="text-sm text-foreground/70 underline hover:text-foreground" href="{{ route('login') }}">
          Voltar a entrar
        </a>

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Enviar link
        </button>
      </div>
    </form>
  </div>
</section>
@endsection
