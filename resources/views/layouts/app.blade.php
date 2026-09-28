<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
  @include('partials.seo')

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    /* Cores do tema — necessárias para as classes com opacidade funcionarem
       (ex.: text-primary-foreground/80, hover:bg-secondary/90, bg-background/95).
       Sem isto, o Tailwind CDN ignora a variante /NN e a cor fica herdada. */
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            background: '#F8F4ED',
            foreground: '#0F253F',
            card: { DEFAULT: '#FFFFFF', foreground: '#0F253F' },
            popover: { DEFAULT: '#FFFFFF', foreground: '#0F253F' },
            primary: { DEFAULT: '#0F253F', foreground: '#FFFFFF' },
            secondary: { DEFAULT: '#C9B29E', foreground: '#0F253F' },
            muted: { DEFAULT: '#EFECE6', foreground: '#6B7280' },
            accent: { DEFAULT: '#C9B29E', foreground: '#0F253F' },
            destructive: { DEFAULT: '#EF4444', foreground: '#FFFFFF' },
            border: '#E5E0D8',
            input: '#E5E0D8',
            ring: '#C9B29E'
          }
        }
      }
    };
  </script>

  <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
  <link rel="icon" href="{{ asset('favicon.png') }}" sizes="any" />
  <link rel="icon" href="{{ asset('icon.png') }}" type="image/png" />
  <link rel="apple-touch-icon" href="{{ asset('apple-icon.png') }}" />
  @yield('head')
</head>
<body class="min-h-screen bg-background text-foreground">

  <!-- Ecrã de loading com logótipo -->
  <div id="page-loader" class="page-loader" role="status" aria-live="polite">
    <x-responsive-image
      path="logo.png"
      class="page-loader__logo"
      fallback="LOGO-HAVREDESIGN.jpeg"
      alt="{{ $site['brand.name'] ?? 'HAVREDESIGN' }}"
      width="220"
      height="110"
      preset="thumbnail"
      loading="eager"
      sizes="220px"
    />
    <div class="page-loader__track" aria-hidden="true"><span class="page-loader__bar"></span></div>
    <span class="sr-only">A carregar…</span>
  </div>

  @include('partials.header')

  @if (session('success'))
    <div id="app-toast" class="fixed bottom-24 left-1/2 -translate-x-1/2 sm:left-auto sm:right-6 sm:translate-x-0 z-[60] max-w-[calc(100vw-2rem)] bg-secondary text-secondary-foreground text-sm font-medium px-5 py-3 rounded-md shadow-lg" role="status">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div id="app-toast" class="fixed bottom-24 left-1/2 -translate-x-1/2 sm:left-auto sm:right-6 sm:translate-x-0 z-[60] max-w-[calc(100vw-2rem)] bg-destructive text-destructive-foreground text-sm font-medium px-5 py-3 rounded-md shadow-lg" role="status">
      {{ session('error') }}
    </div>
  @endif

  <main>
    @yield('content')
  </main>

  @include('partials.footer')
  @include('partials.whatsapp')

  <script>
    // Agenda vinda de `settings.agenda` - lida pelo app.js antes de correr (defer).
    window.HAVRE_AGENDA = @json($agendaJs ?? []);
  </script>
  <script src="{{ asset('js/app.js') }}" defer></script>
  <script>
    (function () {
      var toast = document.getElementById('app-toast');
      if (toast) setTimeout(function () { toast.style.display = 'none'; }, 5000);
    })();
  </script>
  @stack('scripts')
</body>
</html>
