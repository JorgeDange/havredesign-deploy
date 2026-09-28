@props([
    'path',
    'alt' => '',
    'width' => null,
    'height' => null,
    'sizes' => null,
    'fallback' => null,
])

{{-- Variante para imagens ACIMA da dobra: preset 'hero' e loading eager
     (com fetchpriority="high") por omissão. Todo o resto vive em
     components/responsive-image.blade.php. --}}
@include('components.responsive-image', [
    'path' => $path,
    'alt' => $alt,
    'preset' => 'hero',
    'loading' => 'eager',
    'width' => $width,
    'height' => $height,
    'sizes' => $sizes,
    'fallback' => $fallback,
    'attributes' => $attributes,
])
