@extends('layouts.app')

@section('title', 'Nova palavra-passe - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Nova palavra-passe</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">Defina uma nova palavra-passe para a sua conta.</p>

    <form method="POST" action="{{ route('password.store') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="mt-4">
        <x-input-label for="password" value="Nova palavra-passe" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
        <p class="text-xs text-muted-foreground mt-1">Mínimo de 8 caracteres.</p>
      </div>

      <div class="mt-4">
        <x-input-label for="password_confirmation" value="Confirmar palavra-passe" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end mt-6">
        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Repor palavra-passe
        </button>
      </div>
    </form>
  </div>
</section>
@endsection
