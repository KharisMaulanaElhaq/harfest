@props([
    'variant' => 'purple', // purple, white, yellow, pink, dark
    'hover' => true,
])

@php
    $baseStyles = "rounded-3xl border-3 md:border-4 border-hf-purple-900 shadow-hard p-5 md:p-6 transition-all duration-200 relative";
    
    $hoverStyles = $hover ? "hover:-translate-y-1 hover:shadow-hard-lg" : "";

    $variantStyles = match ($variant) {
        'white' => 'bg-white text-gray-900',
        'yellow' => 'bg-hf-yellow-400 text-hf-purple-900',
        'pink' => 'bg-hf-pink-500 text-white',
        'dark' => 'bg-hf-purple-900 text-white',
        default => 'bg-hf-purple-500/90 backdrop-blur-sm text-white',
    };

    $classes = "$baseStyles $variantStyles $hoverStyles";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
