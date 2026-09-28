@extends('layouts.app')

@php
    $projeto = \App\Models\PortfolioItem::published()
        ->where('slug', $slug)
        ->with(['gallery' => fn ($q) => $q->orderBy('sort_order')])
        ->first();

    if (! $projeto) {
        abort(404);
    }

    $galeria = $projeto->gallery
        ->pluck('image_url')
        ->filter(fn ($url) => trim((string) $url) !== '')
        ->values();

    if ($galeria->isEmpty()) {
        $galeria = collect([$projeto->image_url]);
    }

    $metaDescription = \Illuminate\Support\Str::limit(
        trim(strip_tags((string) ($projeto->description ?? ''))),
        155
    );
@endphp

@section('title', $projeto->title . ' - HAVREDESIGN')
@section('meta_description', e($metaDescription))

@section('content')
  <!-- Hero & Project Details -->
  <section id="project-hero" class="relative py-12 md:py-20 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <a href="{{ route('portfolio') }}" class="inline-flex items-center gap-2 text-secondary hover:underline mb-6 text-sm">
        ← Voltar ao Portfólio
      </a>
      <div id="project-header-content">
        <div class="max-w-3xl">
          <span class="text-secondary font-medium text-sm uppercase tracking-wider">{{ $projeto->category }}</span>
          <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight my-4">
            {{ $projeto->title }}
          </h1>
          <p class="text-xl text-primary-foreground/80">
            {{ $projeto->description }}
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Image & Spec Cards -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div id="project-body-content" class="space-y-12 max-w-5xl mx-auto">
        <!-- Meta stats bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-card p-6 rounded-lg border border-border shadow-sm text-center">
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Categoria</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1">{{ $projeto->category }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Área</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1">{{ $projeto->area ?: 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Ano</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1">{{ $projeto->year ?: 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-muted-foreground uppercase font-semibold">Localização</p>
            <p class="font-serif text-lg font-bold text-foreground mt-1">{{ $projeto->location ?: 'Angola' }}</p>
          </div>
        </div>

        <!-- Featured Main Image -->
        <div class="aspect-[16/9] rounded-lg overflow-hidden shadow-lg bg-muted">
          <x-responsive-image path="{{ $projeto->image_url }}" alt="{{ $projeto->title }}" class="object-cover w-full h-full" preset="content" fallback="assets/hero-section.png" />
        </div>

        <!-- Gallery Grid -->
        <div class="space-y-6 pt-8">
          <h3 class="font-serif text-2xl text-foreground">Galeria do Projeto</h3>
          <div class="grid md:grid-cols-2 gap-6">
            @foreach ($galeria as $img)
              <div class="aspect-[4/3] rounded-lg overflow-hidden shadow-sm bg-muted">
                <x-responsive-image path="{{ $img }}" alt="{{ $projeto->title }}" class="object-cover w-full h-full hover:scale-105 transition-transform duration-500" preset="gallery" fallback="assets/hero-section.png" sizes="(min-width:768px) 50vw, 100vw" />
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Gostou deste Projeto?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Entre em contacto connosco e descubra como podemos conceber um espaço sob medida para as suas necessidades.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('solicitar-projeto') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
          Solicitar Projeto Semelhante
        </a>
        <a href="{{ route('agendar') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-primary text-primary font-medium rounded-md hover:bg-primary/10 transition-colors">
          Agendar conversa
        </a>
      </div>
    </div>
  </section>
@endsection
