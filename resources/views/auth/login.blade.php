@extends('layouts.app')

@section('title', 'Entrar - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Entrar</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Aceda à sua área para acompanhar pedidos e agendamentos.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nome@exemplo.ao" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password" value="Palavra-passe" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center gap-2">
          <input id="remember_me" type="checkbox" class="rounded border-border text-primary focus:ring-secondary" name="remember">
          <span class="text-sm text-foreground/80">Manter sessão iniciada</span>
        </label>
      </div>

      <div class="flex items-center justify-between mt-6">
        @if (Route::has('password.request'))
          <a class="text-sm text-foreground/70 underline hover:text-foreground" href="{{ route('password.request') }}">
            Esqueceu a palavra-passe?
          </a>
        @endif

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Entrar
        </button>
      </div>
    </form>

    <p class="text-sm text-foreground/70 mt-6 pt-5 border-t border-border">
      Ainda não tem conta?
      <a href="{{ route('register') }}" class="font-medium text-secondary hover:underline">Registe-se</a>
    </p>
  </div>
</section>
@endsection
