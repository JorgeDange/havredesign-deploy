@extends('layouts.app')

@section('title', 'Networking — HAVREDESIGN')

@section('meta_description', 'Página de networking da HAVREDESIGN - partilhe o nosso QR Code e os contactos com quem quiser falar connosco.')

@section('content')
  <!-- Hero -->
  <section class="py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4 max-w-3xl text-center">
      <span class="text-secondary font-semibold text-sm uppercase tracking-wider">HAVREDESIGN</span>
      <h1 class="font-serif text-4xl md:text-5xl leading-tight mt-2 mb-4">Networking</h1>
      <p class="text-primary-foreground/80">Partilhe esta página.</p>
    </div>
  </section>

  <!-- QR Code -->
  <div class="py-14 md:py-20">
    <div class="container mx-auto px-4 max-w-3xl">
      <div class="max-w-md mx-auto text-center space-y-6">
        <p class="text-muted-foreground leading-relaxed">
          Aponte a câmara do telemóvel ao código abaixo para abrir esta página e guardar os nossos contactos.
        </p>

        @php
          $qrCaminho = \App\Models\Setting::get('networking.qr_image') ?: 'images/qrcode-networking.png';
          $qrExiste = file_exists(public_path($qrCaminho));
        @endphp

        @if ($qrExiste)
          <div class="inline-block bg-white p-4 rounded-lg border border-border shadow-sm">
            <img src="{{ asset($qrCaminho) }}"
                 alt="QR Code para a página Networking da HAVREDESIGN"
                 width="200" height="200"
                 loading="lazy" decoding="async"
                 class="block w-[200px] max-w-full h-auto" />
          </div>
        @else
          <div role="status" class="bg-muted border border-border rounded-lg p-6 text-sm text-muted-foreground">
            O QR Code será publicado em breve.
          </div>
        @endif

        <div class="pt-6 border-t border-border flex flex-wrap gap-4 justify-center">
          <a href="{{ route('contacto') }}" class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors">Falar connosco</a>
        </div>
      </div>
    </div>
  </div>
@endsection
