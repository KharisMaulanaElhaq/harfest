@props([
    'label' => null,
    'price' => null,
    'variant' => 'yellow', // yellow, pink
    'size' => 'md',
])

@php
    $bgClass = match ($variant) {
        'pink' => 'bg-hf-pink-500 text-white border-hf-purple-900',
        default => 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-900',
    };

    $sizeClass = match ($size) {
        'sm' => 'px-3.5 py-1.5 text-xs md:text-sm',
        'lg' => 'px-6 py-3 text-base md:text-lg',
        default => 'px-4 py-2 text-sm md:text-base',
    };
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-between gap-3 border-3 rounded-2xl shadow-hard-sm font-sans font-extrabold $bgClass $sizeClass select-none"]) }}>
    @if ($label)
        <span class="tracking-wide uppercase font-bold text-xs md:text-sm">
            {{ $label }}
        </span>
    @endif

    @if ($price)
        <span class="font-sans font-black text-lg md:text-2xl tracking-normal">
            {{ $price }}
        </span>
    @else
        {{ $slot }}
    @endif
</div>
