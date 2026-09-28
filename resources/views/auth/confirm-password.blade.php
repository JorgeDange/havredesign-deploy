@extends('layouts.app')

@section('title', 'Confirmar palavra-passe - HAVREDESIGN')

@section('content')
<section class="max-w-md mx-auto px-4 py-14 sm:py-20">
  <div class="bg-card border border-border rounded-xl shadow-sm p-8">
    <h1 class="text-2xl font-bold text-foreground">Área segura</h1>
    <p class="text-sm text-muted-foreground mt-1 mb-6">
      Esta é uma área segura da aplicação. Confirme a sua palavra-passe antes de continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
      @csrf

      <div>
        <x-input-label for="password" value="Palavra-passe" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end mt-6">
        <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground text-sm font-medium rounded-md hover:bg-primary/90 transition-colors shadow-sm">
          Confirmar
        </button>
      </div>
    </form>
  </div>
</section>
@endsection
