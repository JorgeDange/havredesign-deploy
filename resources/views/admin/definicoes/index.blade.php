@extends('admin.layout')

@section('title', 'Definições - Administração HAVREDESIGN')
@section('titulo', 'Definições')
@section('subtitulo', 'Contacto, marca, redes sociais, WhatsApp e agenda — as alterações aparecem no site de imediato.')

@section('admin-content')
@php
  $inp = static fn (string $nome): string => 'w-full px-4 py-2.5 rounded-md border '
      . ($errors->has($nome) ? 'border-destructive' : 'border-border')
      . ' bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-secondary';

  $rotulosTipos = [
      'SITE' => 'Visita ao Local',
      'ONLINE' => 'Reunião Online',
  ];
  $codigosTipo = array_keys($rotulosTipos);
  $diasNomes = [0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'];

  $diasAtuais = session()->hasOldInput()
      ? array_map('intval', (array) old('agenda.dias_indisponiveis', []))
      : array_map('intval', (array) ($agenda['dias_indisponiveis'] ?? [0, 6]));

  // `old()` pode devolver array (ex.: campo repetido no POST) — nunca rebentar.
  $comoTexto = static fn ($valor): string => is_array($valor) ? (string) reset($valor) : (string) $valor;

  $tipoPredefinido = $comoTexto(old('agenda.tipo_predefinido', $agenda['tipo_predefinido'] ?? 'ONLINE'));
  $horarios = $comoTexto(old('agenda.horarios', implode("\n", (array) ($agenda['horarios'] ?? []))));
  $horariosSabado = $comoTexto(old('agenda.horarios_sabado', implode("\n", (array) ($agenda['horarios_sabado'] ?? []))));
@endphp

<form method="POST" action="{{ route('definicoes.update') }}" enctype="multipart/form-data" novalidate class="space-y-6">
  @csrf

  <div class="grid gap-6 lg:grid-cols-2 items-start">
    <!-- Contacto -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Contacto</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Aparece no rodapé, na página de contacto e nas mensagens automáticas.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label for="contact_email" class="block text-sm font-medium text-foreground mb-1.5">E-mail</label>
          <input id="contact_email" name="contact[email]" type="email" autocomplete="email"
                 value="{{ old('contact.email', $valores['contact.email'] ?? '') }}"
                 placeholder="info@havredesign.ao"
                 class="{{ $inp('contact.email') }}" />
          @error('contact.email')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="contact_phone_1" class="block text-sm font-medium text-foreground mb-1.5">Telefone 1</label>
          <input id="contact_phone_1" name="contact[phone_1]" type="text" autocomplete="tel"
                 value="{{ old('contact.phone_1', $valores['contact.phone_1'] ?? '') }}"
                 placeholder="+244 926 184 104"
                 class="{{ $inp('contact.phone_1') }}" />
          @error('contact.phone_1')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="contact_phone_2" class="block text-sm font-medium text-foreground mb-1.5">Telefone 2</label>
          <input id="contact_phone_2" name="contact[phone_2]" type="text"
                 value="{{ old('contact.phone_2', $valores['contact.phone_2'] ?? '') }}"
                 placeholder="+244 926 334 650"
                 class="{{ $inp('contact.phone_2') }}" />
          @error('contact.phone_2')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="contact_phone_3" class="block text-sm font-medium text-foreground mb-1.5">Telefone 3</label>
          <input id="contact_phone_3" name="contact[phone_3]" type="text"
                 value="{{ old('contact.phone_3', $valores['contact.phone_3'] ?? '') }}"
                 placeholder="+244 939 718 811"
                 class="{{ $inp('contact.phone_3') }}" />
          @error('contact.phone_3')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="contact_whatsapp" class="block text-sm font-medium text-foreground mb-1.5">WhatsApp (visível)</label>
          <input id="contact_whatsapp" name="contact[whatsapp]" type="text"
                 value="{{ old('contact.whatsapp', $valores['contact.whatsapp'] ?? '') }}"
                 placeholder="+244 926 184 104"
                 class="{{ $inp('contact.whatsapp') }}" />
          @error('contact.whatsapp')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="contact_hours" class="block text-sm font-medium text-foreground mb-1.5">Horário (rodapé)</label>
          <input id="contact_hours" name="contact[hours]" type="text"
                 value="{{ old('contact.hours', $valores['contact.hours'] ?? '') }}"
                 placeholder="Segunda a sexta — 09h às 18h"
                 class="{{ $inp('contact.hours') }}" />
          <p class="text-xs text-muted-foreground mt-1">Texto numa só linha.</p>
          @error('contact.hours')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label for="contact_address_full" class="block text-sm font-medium text-foreground mb-1.5">Morada</label>
          <textarea id="contact_address_full" name="contact[address_full]" rows="2"
                    class="{{ $inp('contact.address_full') }}">{{ old('contact.address_full', $valores['contact.address_full'] ?? '') }}</textarea>
          @error('contact.address_full')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label for="contact_hours_lines" class="block text-sm font-medium text-foreground mb-1.5">Horário (página de contacto)</label>
          <textarea id="contact_hours_lines" name="contact[hours_lines]" rows="3"
                    class="{{ $inp('contact.hours_lines') }}">{{ old('contact.hours_lines', $valores['contact.hours_lines'] ?? '') }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">Um período por linha, tal como deve aparecer no site.</p>
          @error('contact.hours_lines')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <!-- Marca -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Marca</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Nome e slogan usados no site, no SEO e nos e-mails.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label for="brand_name" class="block text-sm font-medium text-foreground mb-1.5">Nome</label>
          <input id="brand_name" name="brand[name]" type="text"
                 value="{{ old('brand.name', $valores['brand.name'] ?? '') }}"
                 placeholder="HAVREDESIGN"
                 class="{{ $inp('brand.name') }}" />
          @error('brand.name')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="brand_legal_name" class="block text-sm font-medium text-foreground mb-1.5">Nome legal</label>
          <input id="brand_legal_name" name="brand[legal_name]" type="text"
                 value="{{ old('brand.legal_name', $valores['brand.legal_name'] ?? '') }}"
                 placeholder="HAVREDESIGN — Arquitetura e Construção, Lda."
                 class="{{ $inp('brand.legal_name') }}" />
          @error('brand.legal_name')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label for="brand_tagline" class="block text-sm font-medium text-foreground mb-1.5">Slogan</label>
          <input id="brand_tagline" name="brand[tagline]" type="text"
                 value="{{ old('brand.tagline', $valores['brand.tagline'] ?? '') }}"
                 placeholder="Arquitetura como Refúgio, excelência em cada detalhe."
                 class="{{ $inp('brand.tagline') }}" />
          @error('brand.tagline')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <!-- Redes sociais -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Redes sociais</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Endereços completos do rodapé, começados por https://.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label for="social_instagram" class="block text-sm font-medium text-foreground mb-1.5">Instagram</label>
          <input id="social_instagram" name="social[instagram]" type="url"
                 value="{{ old('social.instagram', $valores['social.instagram'] ?? '') }}"
                 placeholder="https://www.instagram.com/havredesign.ao/"
                 class="{{ $inp('social.instagram') }}" />
          @error('social.instagram')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="social_facebook" class="block text-sm font-medium text-foreground mb-1.5">Facebook</label>
          <input id="social_facebook" name="social[facebook]" type="url"
                 value="{{ old('social.facebook', $valores['social.facebook'] ?? '') }}"
                 placeholder="https://www.facebook.com/…"
                 class="{{ $inp('social.facebook') }}" />
          @error('social.facebook')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="social_linkedin" class="block text-sm font-medium text-foreground mb-1.5">LinkedIn</label>
          <input id="social_linkedin" name="social[linkedin]" type="url"
                 value="{{ old('social.linkedin', $valores['social.linkedin'] ?? '') }}"
                 placeholder="https://www.linkedin.com/company/…"
                 class="{{ $inp('social.linkedin') }}" />
          @error('social.linkedin')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <!-- WhatsApp -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">WhatsApp</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">Botão flutuante e mensagem pré-preenchida da conversa.</p>

      <div class="grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label for="whatsapp_number" class="block text-sm font-medium text-foreground mb-1.5">Número (só algarismos, com indicativo)</label>
          <input id="whatsapp_number" name="whatsapp[number]" type="text"
                 value="{{ old('whatsapp.number', $valores['whatsapp.number'] ?? '') }}"
                 placeholder="244926184104"
                 class="{{ $inp('whatsapp.number') }}" />
          <p class="text-xs text-muted-foreground mt-1">Usado em https://wa.me/… — ex.: 244926184104.</p>
          @error('whatsapp.number')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:col-span-2">
          <label for="whatsapp_open_message" class="block text-sm font-medium text-foreground mb-1.5">Mensagem inicial</label>
          <textarea id="whatsapp_open_message" name="whatsapp[open_message]" rows="4"
                    class="{{ $inp('whatsapp.open_message') }}">{{ old('whatsapp.open_message', $valores['whatsapp.open_message'] ?? '') }}</textarea>
          @error('whatsapp.open_message')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>

    <!-- Networking (QR Code da página /networking) -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full">
      <h2 class="text-lg font-semibold text-foreground">Networking</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        QR Code da página permanente /networking. O ficheiro carregado substitui sempre
        <span class="font-mono">images/qrcode-networking.png</span>.
      </p>

      <div class="grid gap-4">
        <div>
          <label for="networking_qr_image" class="block text-sm font-medium text-foreground mb-1.5">Imagem do QR Code</label>
          <input id="networking_qr_image" name="qr_image" type="file"
                 accept="image/png,image/jpeg,image/webp"
                 class="block w-full text-sm text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-muted file:text-foreground hover:file:bg-muted/70" />
          <p class="text-xs text-muted-foreground mt-1">
            PNG ou JPEG quadrado (máx. 4 MB), gerado com o endereço https://www.havredesign.ao/networking. Deixe vazio para manter a imagem atual.
          </p>
          @error('qr_image')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        @if ($qrExiste)
          <div>
            <p class="text-sm font-medium text-foreground mb-2">Imagem atual</p>
            <img src="{{ asset($qrCaminho) }}" alt="QR Code atual da página Networking"
                 width="120" height="120"
                 class="block w-[120px] h-[120px] rounded-md border border-border bg-white p-1" />
          </div>
        @endif
      </div>
    </fieldset>

    <!-- Agenda -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full lg:col-span-2">
      <h2 class="text-lg font-semibold text-foreground">Agenda</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        Horários do agendamento online, tipo pré-definido e o texto apresentado para cada tipo de reunião.
      </p>

      <div class="grid gap-4">
        <div>
          <label for="agenda_horarios" class="block text-sm font-medium text-foreground mb-1.5">Horários</label>
          <textarea id="agenda_horarios" name="agenda[horarios]" rows="5" spellcheck="false"
                    class="{{ $inp('agenda.horarios') }} font-mono">{{ $horarios }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">Um horário por linha, no formato HH:MM (ex.: 09:00).</p>
          @error('agenda.horarios')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label for="agenda_horarios_sabado" class="block text-sm font-medium text-foreground mb-1.5">Horários de sábado</label>
          <textarea id="agenda_horarios_sabado" name="agenda[horarios_sabado]" rows="3" spellcheck="false"
                    class="{{ $inp('agenda.horarios_sabado') }} font-mono">{{ $horariosSabado }}</textarea>
          <p class="text-xs text-muted-foreground mt-1">Um horário por linha. Pode ficar vazio.</p>
          @error('agenda.horarios_sabado')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <span class="block text-sm font-medium text-foreground mb-1.5">Dias indisponíveis</span>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach ($diasNomes as $numero => $dia)
              <label class="flex items-center gap-2 px-3 py-2 rounded-md border {{ $errors->has('agenda.dias_indisponiveis.*') ? 'border-destructive' : 'border-border' }} bg-card text-sm text-foreground cursor-pointer hover:bg-muted transition-colors">
                <input type="checkbox" name="agenda[dias_indisponiveis][]" value="{{ $numero }}"
                       {{ in_array($numero, $diasAtuais, true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-border" />
                {{ $dia }}
              </label>
            @endforeach
          </div>
          <p class="text-xs text-muted-foreground mt-1">Marque os dias em que não é possível agendar.</p>
          @error('agenda.dias_indisponiveis.*')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:max-w-md">
          <label for="agenda_tipo_predefinido" class="block text-sm font-medium text-foreground mb-1.5">Tipo de reunião pré-definido</label>
          <select id="agenda_tipo_predefinido" name="agenda[tipo_predefinido]"
                  class="{{ $inp('agenda.tipo_predefinido') }}">
            @foreach ($rotulosTipos as $codigo => $rotulo)
              <option value="{{ $codigo }}" {{ $tipoPredefinido === $codigo ? 'selected' : '' }}>
                {{ $codigo }} — {{ $agenda['tipos'][$codigo]['label'] ?? $rotulo }}
              </option>
            @endforeach
          </select>
          @error('agenda.tipo_predefinido')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="border-t border-border pt-5 mt-2">
          <h3 class="text-sm font-semibold text-foreground mb-1">Textos dos tipos de reunião</h3>
          <p class="text-xs text-muted-foreground mb-4">
            Rótulo e nota apresentados no site, no resumo do agendamento e nos e-mails.
          </p>

          <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($codigosTipo as $codigo)
              @php
                $campoLabel = "agenda.tipos.{$codigo}.label";
                $campoNota  = "agenda.tipos.{$codigo}.nota";
                $tipoAtual  = is_array($agenda['tipos'][$codigo] ?? null) ? $agenda['tipos'][$codigo] : [];
              @endphp

              <div class="rounded-lg border border-border bg-background p-4 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary">{{ $codigo }}</p>

                <div>
                  <label for="agenda_tipo_label_{{ $codigo }}" class="block text-sm font-medium text-foreground mb-1.5">Rótulo</label>
                  <input id="agenda_tipo_label_{{ $codigo }}" name="agenda[tipos][{{ $codigo }}][label]" type="text" maxlength="120"
                         value="{{ old($campoLabel, $tipoAtual['label'] ?? $codigo) }}"
                         class="{{ $inp($campoLabel) }}" />
                  @error($campoLabel)<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                  <label for="agenda_tipo_nota_{{ $codigo }}" class="block text-sm font-medium text-foreground mb-1.5">Nota</label>
                  <textarea id="agenda_tipo_nota_{{ $codigo }}" name="agenda[tipos][{{ $codigo }}][nota]" rows="3" maxlength="300"
                            class="{{ $inp($campoNota) }}">{{ old($campoNota, $tipoAtual['nota'] ?? '') }}</textarea>
                  <p class="text-xs text-muted-foreground mt-1">Deixe vazio para não mostrar nota.</p>
                  @error($campoNota)<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
                </div>
              </div>
            @endforeach
          </div>

          <p class="text-xs text-muted-foreground mt-3">
            A morada só é pedida na visita ao local — esse comportamento não é editável aqui.
          </p>
        </div>
      </div>
    </fieldset>

    <!-- Caso real (página Processo) -->
    <fieldset class="bg-card border border-border rounded-xl shadow-sm p-6 max-w-2xl w-full lg:col-span-2">
      <h2 class="text-lg font-semibold text-foreground">Caso real</h2>
      <p class="text-sm text-muted-foreground mt-1 mb-5">
        Local apresentado no estudo de caso «Casa Vila Nova», na página Processo.
      </p>

      <div class="grid gap-4">
        <div class="sm:max-w-2xl">
          <label for="process_case_local" class="block text-sm font-medium text-foreground mb-1.5">Local</label>
          <input id="process_case_local" name="process[case_local]" type="text" maxlength="255"
                 value="{{ old('process.case_local', $valores['process.case_local'] ?? '') }}"
                 placeholder="Benfica, Via Expressa, Bairro Tchinguari, Rua 1, Talatona, Luanda"
                 class="{{ $inp('process.case_local') }}" />
          <p class="text-xs text-muted-foreground mt-1">Deixe vazio para esconder o campo no site.</p>
          @error('process.case_local')<p class="text-xs text-destructive mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </fieldset>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-6">
    <p class="text-sm text-muted-foreground">Todas as secções são guardadas num único passo.</p>
    <button type="submit"
            class="inline-flex items-center justify-center px-6 py-3 bg-secondary text-secondary-foreground text-sm font-medium rounded-md hover:bg-secondary/90 transition-colors">
      Guardar definições
    </button>
  </div>
</form>
@endsection
