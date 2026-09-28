@php
    $user = auth()->user();

    $navLinks = [
        ['label' => 'Início',    'href' => '/',                 'active' => request()->routeIs('home')],
        ['label' => 'Sobre',     'href' => route('sobre'),      'active' => request()->routeIs('sobre')],
        ['label' => 'Serviços',  'href' => route('servicos'),   'active' => request()->routeIs('servicos')],
        ['label' => 'Orçamento', 'href' => route('orcamentos'), 'active' => request()->routeIs('orcamentos')],
        ['label' => 'Portfólio', 'href' => route('portfolio'),  'active' => request()->routeIs('portfolio*')],
        ['label' => 'Processo',  'href' => route('processo'),   'active' => request()->routeIs('processo')],
        ['label' => 'Contacto',  'href' => route('contacto'),   'active' => request()->routeIs('contacto')],
        ['label' => 'Networking', 'href' => route('networking'), 'active' => request()->routeIs('networking')],
    ];
@endphp

<header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 transition-all duration-300">
  <div class="container mx-auto flex h-20 items-center justify-between px-4">
    <a href="{{ url('/') }}" class="flex items-center gap-2 hover:opacity-90 transition-opacity">
      <x-responsive-image path="logo.png" width="120" height="60" alt="Logo HavreDesign"
        preset="thumbnail" loading="eager" sizes="120px"
        fallback="LOGO-HAVREDESIGN.jpeg" />
    </a>

    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center gap-8">
      @foreach ($navLinks as $link)
        <a href="{{ $link['href'] }}"
           class="text-sm font-medium {{ $link['active'] ? 'text-secondary font-semibold border-b-2 border-secondary pb-1' : 'text-foreground/80 hover:text-primary' }} transition-colors">
          {{ $link['label'] }}
        </a>
      @endforeach
    </nav>

    <div class="hidden lg:flex items-center gap-4">
      @auth
        <div class="relative group">
          <button class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-muted text-sm font-medium text-foreground transition-colors">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>{{ $user->name }}</span>
          </button>
          <div class="absolute right-0 top-full hidden group-hover:block bg-card border border-border shadow-lg rounded-md w-48 py-1 z-50 animate-fade-in-up">
            <a href="/conta" class="block px-4 py-2 text-sm text-foreground hover:bg-muted">Minha Área</a>
            @if ($user->isAdmin())
              <a href="{{ route('admin') }}" class="block px-4 py-2 text-sm text-foreground hover:bg-muted font-semibold text-secondary">Painel Admin</a>
            @endif
            <hr class="border-border my-1" />
            <form method="POST" action="/sair">
              @csrf
              <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-muted">Sair</button>
            </form>
          </div>
        </div>
      @else
        <a href="/entrar" class="px-4 py-2 text-sm font-medium text-foreground hover:text-primary transition-colors">
          Entrar
        </a>
      @endauth
      <a href="{{ route('agendar') }}" class="px-5 py-2.5 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors shadow-sm hover:shadow">
        Iniciar Projeto
      </a>
    </div>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="lg:hidden p-3 -mr-2 text-foreground" aria-label="Toggle Menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>

  <!-- Mobile Navigation Drawer -->
  <div id="mobile-menu" class="hidden lg:hidden border-t border-border bg-background px-4 py-4 space-y-3 animate-fade-in">
    @foreach ($navLinks as $link)
      <a href="{{ $link['href'] }}" class="block text-sm font-medium py-2 {{ $link['active'] ? 'text-secondary font-bold' : 'text-foreground/80' }}">
        {{ $link['label'] }}
      </a>
    @endforeach
    <div class="pt-4 border-t border-border space-y-2">
      @auth
        <a href="/conta" class="block w-full text-center py-2 px-4 border border-border rounded-md text-sm font-medium">Minha Área</a>
        @if ($user->isAdmin())
          <a href="{{ route('admin') }}" class="block w-full text-center py-2 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium">Painel Admin</a>
        @endif
        <form method="POST" action="/sair">
          @csrf
          <button type="submit" class="block w-full text-center py-2 text-sm text-red-600">Sair</button>
        </form>
      @else
        <a href="/entrar" class="block w-full text-center py-2 px-4 border border-border rounded-md text-sm font-medium">Entrar</a>
      @endauth
      <a href="{{ route('agendar') }}" class="block w-full text-center py-2 px-4 bg-secondary text-secondary-foreground rounded-md text-sm font-medium">
        Iniciar Projeto
      </a>
    </div>
  </div>
</header>

<script>
  (function () {
    var btn = document.getElementById('mobile-menu-btn');
    var menu = document.getElementById('mobile-menu');
    if (btn && menu) {
      btn.addEventListener('click', function () { menu.classList.toggle('hidden'); });
    }
  })();
</script>
