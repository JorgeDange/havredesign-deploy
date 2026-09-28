<title>@yield('title', trim(($site['brand.name'] ?? 'HAVREDESIGN') . ' - Arquitetura como Refúgio'))</title>
<meta name="description" content="@yield('meta_description', 'HAVREDESIGN — atelier de arquitetura e design em Luanda. Projeto arquitetónico, design de interiores e fiscalização. Arquitetura como refúgio, excelência em cada detalhe.')" />
<meta property="og:title" content="@yield('title', trim(($site['brand.name'] ?? 'HAVREDESIGN') . ' - Arquitetura como Refúgio'))" />
<meta property="og:description" content="@yield('meta_description', 'Projeto arquitetónico, design de interiores e fiscalização em Luanda. Arquitetura como refúgio, excelência em cada detalhe.')" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
