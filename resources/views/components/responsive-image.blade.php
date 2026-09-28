@props([
    'path',
    'alt' => '',
    'preset' => 'content',
    'loading' => 'lazy',
    'width' => null,
    'height' => null,
    'sizes' => null,
    'fallback' => null,
])

@php
    /*
     * Componente de imagem responsiva do HAVREDESIGN.
     *
     * - Usa o serviço do zoker/responsive-images para gerar as variantes WebP
     *   (o pacote só sabe gerar WebP; os originais são png/jpeg/jpg).
     * - `preset` escolhe as larguras (config/responsive-images.php).
     * - Nunca faz upscale: a largura alvo é min(largura máxima do preset,
     *   largura real do ficheiro).
     * - O <img> de fallback aponta para o ORIGINAL (png/jpeg/jpg), para que um
     *   browser sem WebP continue a ver a imagem.
     * - O alias `responsive-image` é redirecionado para esta vista no
     *   AppServiceProvider (o pacote regista uma classe com o mesmo nome).
     */

    $fallbackUrl = ($fallback !== null && trim((string) $fallback) !== '')
        ? (str_contains($fallback, '://') ? $fallback : asset(ltrim($fallback, '/')))
        : null;

    $rawPath = ltrim(trim((string) $path), '/');
    $isRemote = str_contains($rawPath, '://');

    $disk = config('responsive-images.disk', 'web');
    $presets = config('responsive-images.presets', []);
    $presetWidths = $presets[$preset] ?? ($presets['content'] ?? [1200, 800, 400]);

    $storage = \Illuminate\Support\Facades\Storage::disk($disk);
    $image = null;
    $originalWidth = null;
    $originalHeight = null;
    $src = $isRemote
        ? $rawPath
        : ($rawPath !== '' ? $storage->url($rawPath) : ($fallbackUrl ?? ''));

    if ($rawPath !== '' && ! $isRemote && $storage->exists($rawPath)) {
        // Só lê o cabeçalho (rápido) — serve para CLS e para travar o upscale.
        $info = @getimagesize($storage->path($rawPath));

        if (is_array($info) && $info[0] > 0 && $info[1] > 0) {
            $originalWidth = $info[0];
            $originalHeight = $info[1];

            $target = min(max($presetWidths), $originalWidth);
            $previousBreakpoints = config('responsive-images.breakpoints');
            config()->set('responsive-images.breakpoints', $presetWidths);

            try {
                $image = app(\Zoker\ResponsiveImages\ResponsiveImagesService::class)
                    ->make($rawPath, $target, null, $disk);
            } catch (\Throwable $e) {
                report($e);
                $image = null;
            } finally {
                config()->set('responsive-images.breakpoints', $previousBreakpoints);
            }
        }
    }

    $width = $width ?: $originalWidth;
    $height = $height ?: $originalHeight;
    $sizes = $sizes ?: '100vw';

    $srcset = $image?->srcset ?? '';
    $hasSource = $image !== null && $srcset !== '' && $image->hasSource();

    if ($loading === 'eager' && ! $attributes->has('fetchpriority')) {
        $attributes = $attributes->merge(['fetchpriority' => 'high']);
    }

    if ($fallbackUrl !== null) {
        $attributes = $attributes->merge([
            'onerror' => "this.onerror=null;this.src='{$fallbackUrl}'",
        ]);
    }
@endphp

<picture>
    @if ($hasSource)
        <source type="image/webp" srcset="{{ $srcset }}" sizes="{{ $sizes }}">
    @endif
    <img
        src="{{ $src }}"
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif
        loading="{{ $loading }}"
        decoding="async"
        alt="{{ $alt }}"
        {{ $attributes }}
    >
</picture>
