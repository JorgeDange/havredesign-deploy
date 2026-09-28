@extends('layouts.app')

@section('title', 'Processo de Trabalho - HAVREDESIGN')

@section('content')
  <!-- Hero Section -->
  <section class="relative py-14 md:py-24 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl">
        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl leading-tight mb-6">
          Processo de Trabalho
        </h1>
        <p class="text-xl text-primary-foreground/80">
          Conheça as etapas que seguimos para transformar ideias em espaços funcionais, acolhedores e personalizados, desenvolvidos de acordo com as necessidades e identidade de cada cliente.
        </p>
      </div>
    </div>
  </section>

  <!-- Processo de Trabalho -->
  <section class="py-14 md:py-24">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto">
        <div class="mb-12 space-y-3">
          <span class="text-secondary font-semibold text-sm uppercase tracking-wider">Etapas</span>
          <h2 class="font-serif text-3xl md:text-4xl text-foreground">Processo de Trabalho</h2>
        </div>
        <div class="space-y-12">
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">1</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">BRIEFING</h3>
              <p class="text-muted-foreground leading-relaxed">Analisamos as suas necessidades, seus objectivos e as suas expectativas.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">2</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">CONCEITUAÇÃO</h3>
              <p class="text-muted-foreground leading-relaxed">Damos identidade ao projeto, definindo ideias arquitetónicas.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">3</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">PRODUÇÃO</h3>
              <p class="text-muted-foreground leading-relaxed">Elaboramos peças desenhadas, modelos tridimensionais, e definimos os materiais para a execução correta do projeto.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">4</div>
              <div class="w-0.5 h-full bg-border mt-4"></div>
            </div>
            <div class="pb-12">
              <h3 class="font-serif text-2xl text-foreground mb-3">APROVAÇÃO</h3>
              <p class="text-muted-foreground leading-relaxed">Apresentação do projeto para sua validação e ajustes finais, respondendo plenamente às suas expectativas.</p>
            </div>
          </div>
          <div class="flex gap-6">
            <div class="flex flex-col items-center">
              <div class="w-14 h-14 rounded-full bg-secondary text-secondary-foreground flex items-center justify-center text-xl font-serif font-bold shrink-0">5</div>
            </div>
            <div>
              <h3 class="font-serif text-2xl text-foreground mb-3">ENTREGA</h3>
              <p class="text-muted-foreground leading-relaxed">Disponibilizamos todos os elementos técnicos necessários para a execução do projeto.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Notas de adaptação e âmbito -->
  <section class="py-16 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-8">
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Etapas adaptadas</h3>
          <p class="text-primary-foreground/80 text-sm leading-relaxed">
            As etapas são adaptadas à natureza do serviço e ao âmbito definido em cada proposta.
          </p>
        </div>
        <div class="space-y-3 p-6 bg-white/5 rounded-lg border border-white/10">
          <h3 class="font-serif text-xl text-secondary">Sobre a execução da obra</h3>
          <p class="text-primary-foreground/80 text-sm leading-relaxed">
            A HAVREDESIGN presta fiscalização e acompanhamento técnico quando contratados.
            A construção não integra a oferta atual da empresa.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Case Study Section -->
  <section class="py-14 md:py-24 bg-muted">
    <div class="container mx-auto px-4">
      <div class="text-center mb-16">
        <span class="text-secondary font-semibold text-sm uppercase tracking-wider">Caso Real</span>
        <h2 class="font-serif text-3xl md:text-4xl text-foreground mt-2 mb-4">Casa Vila Nova</h2>
        <p class="text-muted-foreground max-w-2xl mx-auto">Acompanhe a transformação completa de uma residência de 380m², desde o conceito inicial até a entrega final.</p>
      </div>

      <!-- Project Info Badges -->
      <div class="flex flex-wrap justify-center gap-8 mb-16">
        <div class="flex items-center gap-3 bg-card p-4 rounded-lg border border-border shadow-sm">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div>
            <p class="text-xs text-muted-foreground">Duração</p>
            <p class="font-medium text-foreground">6 meses</p>
          </div>
        </div>
        <div class="flex items-center gap-3 bg-card p-4 rounded-lg border border-border shadow-sm">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-2V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
          <div>
            <p class="text-xs text-muted-foreground">Área</p>
            <p class="font-medium text-foreground">380m²</p>
          </div>
        </div>
        @if (!empty($site['process.case_local']))
        <div class="flex items-center gap-3 bg-card p-4 rounded-lg border border-border shadow-sm">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <div>
            <p class="text-xs text-muted-foreground">Local</p>
            <p class="font-medium text-foreground">{{ $site['process.case_local'] }}</p>
          </div>
        </div>
        @endif
      </div>

      <!-- Phases Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="group">
          <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-card shadow-sm">
            <x-responsive-image path="assets/processo/image1.png" alt="Planta Original" class="object-cover w-full h-full opacity-90 group-hover:opacity-100 transition-opacity" preset="card" sizes="(min-width:1024px) 33vw, (min-width:768px) 50vw, 100vw" />
          </div>
          <h3 class="font-serif text-lg text-foreground mb-2">1. Planta Original</h3>
          <p class="text-sm text-muted-foreground">Estado inicial do imóvel antes da intervenção, com layout tradicional e ambientes compartimentados.</p>
        </div>
        <div class="group">
          <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-card shadow-sm">
            <x-responsive-image path="assets/portifolio/image1.png" alt="Nova Planta" class="object-cover w-full h-full opacity-90 group-hover:opacity-100 transition-opacity" preset="card" sizes="(min-width:1024px) 33vw, (min-width:768px) 50vw, 100vw" />
          </div>
          <h3 class="font-serif text-lg text-foreground mb-2">2. Nova Planta</h3>
          <p class="text-sm text-muted-foreground">Proposta de novo layout com conceito aberto, integrando sala, cozinha e varanda.</p>
        </div>
        <div class="group">
          <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-card shadow-sm">
            <x-responsive-image path="assets/portifolio/image2.png" alt="Renderização 3D" class="object-cover w-full h-full opacity-90 group-hover:opacity-100 transition-opacity" preset="card" sizes="(min-width:1024px) 33vw, (min-width:768px) 50vw, 100vw" />
          </div>
          <h3 class="font-serif text-lg text-foreground mb-2">3. Renderização 3D</h3>
          <p class="text-sm text-muted-foreground">Visualização do projeto em 3D para aprovação do cliente antes da execução.</p>
        </div>
        <div class="group">
          <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-card shadow-sm">
            <x-responsive-image path="assets/portifolio/image3.png" alt="Durante a obra" class="object-cover w-full h-full opacity-90 group-hover:opacity-100 transition-opacity" preset="card" sizes="(min-width:1024px) 33vw, (min-width:768px) 50vw, 100vw" />
          </div>
          <h3 class="font-serif text-lg text-foreground mb-2">4. Durante a obra</h3>
          <p class="text-sm text-muted-foreground">Registo do processo de obra, com fiscalização e acompanhamento técnico contratados.</p>
        </div>
        <div class="group">
          <div class="relative aspect-[4/3] rounded-lg overflow-hidden mb-4 bg-card shadow-sm">
            <x-responsive-image path="assets/portifolio/image4.png" alt="Resultado Final" class="object-cover w-full h-full opacity-90 group-hover:opacity-100 transition-opacity" preset="card" sizes="(min-width:1024px) 33vw, (min-width:768px) 50vw, 100vw" />
          </div>
          <h3 class="font-serif text-lg text-foreground mb-2">5. Resultado Final</h3>
          <p class="text-sm text-muted-foreground">Ambiente finalizado com mobiliário, iluminação e decoração completa.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-14 md:py-24 bg-secondary">
    <div class="container mx-auto px-4 text-center">
      <h2 class="font-serif text-3xl md:text-4xl text-secondary-foreground mb-4">
        Pronto para Iniciar Seu Projeto?
      </h2>
      <p class="text-secondary-foreground/80 max-w-2xl mx-auto mb-8">
        Agende uma consulta e vamos começar a transformar seu espaço juntos.
      </p>
      <a href="{{ route('agendar') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground font-medium rounded-md hover:bg-primary/90 transition-colors">
        Agendar conversa
        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </section>
@endsection
