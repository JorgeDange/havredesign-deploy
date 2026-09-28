@extends('layouts.app')

@section('title', 'HAVREDESIGN - Arquitetura como Refúgio')
@section('meta_description', 'Criamos projetos arquitetónicos que unem funcionalidade, identidade, conforto e sofisticação, concebendo espaços que acolhem e inspiram.')

@php
    // ui/index.html → #home-services-grid: top 3 serviços activos não-complementares
    // (JS: getServices().filter(s => s.active && s.group !== 'complementar').sort(...).slice(0, 3))
    $servicos = \App\Models\Service::active()->principal()->orderBy('sort_order')->take(3)->get();

    // ui/index.html → #home-portfolio-grid: primeiros 4 (1 principal + 3 secundários)
    // (JS: getPortfolio().slice(0, 4) — no Laravel só projetos publicados)
    $projetos = \App\Models\PortfolioItem::published()->orderBy('sort_order')->take(4)->get();
    $projetoPrincipal = $projetos->first();
    $projetosSecundarios = $projetos->slice(1)->values();

    // Mesmos SVGs Lucide de ui/js/app.js (renderLucideIcon) — só os `path`s.
    $lucide = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/>',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>',
        'wrench' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'lightbulb' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        'map-pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'maximize' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>',
        'image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'dollar-sign' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1v22m5-18H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'trees' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7m-7-7v18"/>',
    ];
@endphp

@section('content')
  <!-- Hero Section -->
  <section class="relative min-h-[90vh] flex items-center bg-primary">
    <div class="absolute inset-0 bg-primary/95">
      <div
        class="absolute inset-0 opacity-20"
        style="background-image: url('{{ asset('assets/hero-section.png') }}'); background-size: cover; background-position: center;"
      ></div>
    </div>
    <div class="relative container mx-auto px-4 py-14 md:py-24">
      <div class="max-w-3xl">
        <h1 class="font-serif text-2xl md:text-4xl lg:text-6xl text-primary-foreground leading-tight mb-6">
          <span class="italic">HAVREDESIGN - Arquitetura como refúgio</span>
        </h1>
        <p class="text-lg md:text-xl text-primary-foreground mb-8 max-w-xl">
          Criamos projetos arquitetónicos que unem funcionalidade, identidade, conforto e sofisticação, concebendo espaços que acolhem e inspiram.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="{{ route('solicitar-projeto') }}" class="inline-flex items-center justify-center px-8 py-4 bg-secondary text-secondary-foreground font-medium rounded-md hover:bg-secondary/90 transition-colors text-base">
            Solicitar orçamento
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
          <a href="{{ route('agendar') }}" class="inline-flex items-center justify-center px-8 py-4 border border-primary-foreground/30 text-white font-medium rounded-md hover:bg-white/10 transition-colors text-base">
            Agendar conversa
          </a>
          <a href="{{ route('portfolio') }}" id="hero-portfolio-cta" class="inline-flex items-center justify-center px-8 py-4 text-white/80 font-medium rounded-md hover:text-white hover:bg-white/10 transition-colors text-base {{ $projetos->isEmpty() ? 'hidden' : '' }}">
            Ver portefólio
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-14 md:py-24 bg-muted">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="relative aspect-[4/3] rounded-lg overflow-hidden shadow-md">
          <x-responsive-image path="assets/page-image/sobre.png"
            alt="Interior elegante"
            class="object-cover w-full h-full"
            preset="content"
            sizes="(min-width:1024px) 50vw, 100vw"
          />
        </div>
        <div class="space-y-6">
          <h2 class="font-serif text-3xl md:text-4xl text-foreground">Sobre</h2>
          <p class="text-muted-foreground leading-relaxed">
            A HAVREDESIGN é um atelier de arquitetura e design que acredita que os espaços têm o poder de transformar a forma como as pessoas vivem, trabalham e se relacionam com o mundo.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Com uma abordagem contemporânea, funcional e sensível, desenvolvemos projetos que unem estética, criatividade e sofisticação, criando ambientes pensados para acolher, inspirar e proporcionar bem-estar.
          </p>
          <p class="text-muted-foreground leading-relaxed">
            Mais do que projetar espaços, criamos experiências com identidade, equilíbrio e propósito — sempre com um olhar atento aos detalhes, à inovação e à harmonia entre funcionalidade e beleza.
          </p>
          <a href="{{ route('sobre') }}" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground rounded-md hover:bg-card transition-colors mt-4 font-medium">
            Saiba Mais
            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground mb-4">Nossos Serviços</h2>
        <p class="text-muted-foreground max-w-2xl mx-auto">
          Soluções personalizadas de arquitetura, interiores e apoio técnico especializado para transformar o seu espaço.
        </p>
      </div>
      <div id="home-services-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($servicos as $service)
          <div class="group hover:shadow-lg transition-shadow bg-secondary/30 border-0 rounded-lg p-6 space-y-4">
            <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
              <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $lucide[$service->icon] ?? $lucide['home'] !!}</svg>
            </div>
            <h3 class="font-serif text-xl text-foreground">{{ $service->title }}</h3>
            <p class="text-sm text-muted-foreground">{{ $service->description }}</p>
          </div>
        @endforeach
      </div>
      <div class="text-center mt-12">
        <a href="{{ route('servicos') }}" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground rounded-md hover:bg-muted transition-colors font-medium">
          Conhecer serviços
          <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Soluções para diferentes necessidades -->
  <section class="py-14 md:py-24 bg-muted">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16 space-y-4">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground">Soluções para diferentes necessidades</h2>
        <p class="text-muted-foreground max-w-2xl mx-auto">
          Adaptamos a nossa abordagem ao tipo de projeto e ao momento de vida de cada cliente.
        </p>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-card border border-border rounded-lg p-8 space-y-4 shadow-sm">
          <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/></svg>
          </div>
          <h3 class="font-serif text-xl text-foreground">Investimento imobiliário residencial</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">
            Projetos pensados para valorizar o investimento, com decisão técnica informada e atenção ao retorno do capital imobilizado.
          </p>
        </div>
        <div class="bg-card border border-border rounded-lg p-8 space-y-4 shadow-sm">
          <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
          </div>
          <h3 class="font-serif text-xl text-foreground">Habitação própria</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">
            Espaços que respondem à forma como vive e se identifica, com conforto, identidade e decisões adequadas ao orçamento familiar.
          </p>
        </div>
        <div class="bg-card border border-border rounded-lg p-8 space-y-4 shadow-sm">
          <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-secondary-foreground">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <h3 class="font-serif text-xl text-foreground">Comércio e serviços</h3>
          <p class="text-sm text-muted-foreground leading-relaxed">
            Ambientes que reforçam a identidade da marca, organizam fluxos e valorizam a experiência de quem entra no espaço.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Process Preview -->
  <section class="py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="font-serif text-3xl md:text-4xl mb-4">Processo de Trabalho</h2>
        <p class="text-primary-foreground/80 max-w-2xl mx-auto">
          Conheça as etapas que seguimos para transformar seu espaço em um ambiente único.
        </p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-8">
        <div class="text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mx-auto text-2xl font-serif font-bold">
            01
          </div>
          <h3 class="font-serif text-xl">BRIEFING</h3>
          <p class="text-primary-foreground/70 text-sm">Analisamos as suas necessidades, seus objectivos e as suas expectativas.</p>
        </div>
        <div class="text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mx-auto text-2xl font-serif font-bold">
            02
          </div>
          <h3 class="font-serif text-xl">CONCEITUAÇÃO</h3>
          <p class="text-primary-foreground/70 text-sm">Damos identidade ao projeto, definindo ideias arquitetónicas.</p>
        </div>
        <div class="text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mx-auto text-2xl font-serif font-bold">
            03
          </div>
          <h3 class="font-serif text-xl">PRODUÇÃO</h3>
          <p class="text-primary-foreground/70 text-sm">Elaboramos peças desenhadas, modelos tridimensionais, e definimos os materiais para a execução correta do projeto.</p>
        </div>
        <div class="text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mx-auto text-2xl font-serif font-bold">
            04
          </div>
          <h3 class="font-serif text-xl">APROVAÇÃO</h3>
          <p class="text-primary-foreground/70 text-sm">Apresentação do projeto para sua validação e ajustes finais, respondendo plenamente às suas expectativas.</p>
        </div>
        <div class="text-center space-y-4">
          <div class="w-16 h-16 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center mx-auto text-2xl font-serif font-bold">
            05
          </div>
          <h3 class="font-serif text-xl">ENTREGA</h3>
          <p class="text-primary-foreground/70 text-sm">Disponibilizamos todos os elementos técnicos necessários para a execução do projeto.</p>
        </div>
      </div>
      <div class="text-center mt-12">
        <a href="{{ route('processo') }}" class="inline-flex items-center justify-center px-6 py-3 border border-primary-foreground/30 text-white rounded-md hover:bg-white/10 transition-colors font-medium">
          Ver Processo Completo
          <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Portfolio Preview -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <h2 class="font-serif text-3xl md:text-4xl text-foreground mb-4">Portfólio</h2>
        <p class="text-muted-foreground max-w-2xl mx-auto">
          Conheça alguns dos projetos que realizamos e inspire-se.
        </p>
      </div>
      <div id="home-portfolio-grid" class="grid md:grid-cols-2 gap-6">
        @if ($projetos->isNotEmpty())
          <a href="{{ route('portfolio.show', $projetoPrincipal->slug) }}" class="relative aspect-[4/3] rounded-lg overflow-hidden group block">
            <x-responsive-image path="{{ $projetoPrincipal->image_url }}" alt="{{ $projetoPrincipal->title }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500" preset="card" fallback="assets/hero-section.png" sizes="(min-width:768px) 50vw, 100vw" />
            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent flex items-end p-6">
              <div>
                <span class="text-secondary text-sm">{{ $projetoPrincipal->category }}</span>
                <h3 class="font-serif text-xl text-primary-foreground">{{ $projetoPrincipal->title }}</h3>
              </div>
            </div>
          </a>
          @if ($projetosSecundarios->isNotEmpty())
            <div class="grid grid-cols-2 gap-6">
              @foreach ($projetosSecundarios as $projeto)
                <a href="{{ route('portfolio.show', $projeto->slug) }}" class="relative rounded-lg overflow-hidden group block {{ $loop->index === 2 ? 'col-span-2' : '' }}">
                  <div class="{{ $loop->index === 2 ? 'aspect-[4/3]' : 'aspect-square' }} relative">
                    <x-responsive-image path="{{ $projeto->image_url }}" alt="{{ $projeto->title }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500" preset="card" fallback="assets/hero-section.png" sizes="(min-width:768px) 50vw, 100vw" />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent flex items-end p-4">
                      <div>
                        <span class="text-secondary text-xs">{{ $projeto->category }}</span>
                        <h3 class="font-serif text-sm text-primary-foreground">{{ $projeto->title }}</h3>
                      </div>
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          @endif
        @endif
      </div>
      <div class="text-center mt-12">
        <a href="{{ route('portfolio') }}" class="inline-flex items-center justify-center px-6 py-3 border border-border text-foreground rounded-md hover:bg-muted transition-colors font-medium">
          Ver portefólio
          <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Secção de testemunhos omitida (está com "hidden" + data-testimonials="pending-update" no ui/) -->

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Pronto para Transformar seu Espaço?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Agende uma consulta gratuita e descubra como podemos transformar seus sonhos em realidade.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('agendar') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
          Agendar conversa
          <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </a>
        <a href="{{ route('solicitar-projeto') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-primary text-primary font-medium rounded-md hover:bg-primary/10 transition-colors">
          Solicitar orçamento
        </a>
      </div>
    </div>
  </section>
@endsection
