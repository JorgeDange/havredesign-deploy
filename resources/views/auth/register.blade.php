@extends('layouts.app')

@section('title', 'Criar conta - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Criar conta</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Registe-se para acompanhar os seus pedidos e agendamentos.</p>

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nome@exemplo.ao" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password" value="Palavra-passe" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
        <p class="text-xs text-muted-foreground mt-1">Mínimo de 8 caracteres.</p>
      </div>

      <div class="mt-4">
        <x-input-label for="password_confirmation" value="Confirmar palavra-passe" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>

      <div class="flex items-center justify-between mt-6">
        <a class="text-sm text-foreground/70 underline hover:text-foreground" href="{{ route('login') }}">
          Já tem conta? Entrar
        </a>

        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Registar
        </button>
      </div>
    </form>
  </div>
</section>
@endsection
