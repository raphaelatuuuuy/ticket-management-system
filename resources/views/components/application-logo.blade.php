@php
    $logoPath = resource_path('images/logo.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

    // larger default width but keep height automatic to avoid stretching
    $defaultClass = 'w-28 h-auto object-contain';
    $attrs = $attributes->get('class') ? $attributes : $attributes->merge(['class' => $defaultClass]);
@endphp

@if ($logoData)
    <img {{ $attrs->merge(['alt' => config('app.name'), 'src' => 'data:image/png;base64,'.$logoData]) }} />
@else
    <img {{ $attrs->merge(['alt' => config('app.name'), 'src' => asset('images/logo.png')]) }} />
@endif
