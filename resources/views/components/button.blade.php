@props([
    'variant' => 'primary', // primary, secondary, ghost, dark
    'size' => 'md',       // sm, md, lg
    'href' => null,
    'type' => 'button',
])

@php
    $baseStyles = "inline-flex items-center justify-center font-sans font-extrabold tracking-wider uppercase rounded-2xl btn-press select-none cursor-pointer focus:outline-none focus:ring-4 focus:ring-hf-yellow-400/50";

    $sizeStyles = match ($size) {
        'sm' => 'px-3.5 py-1.5 text-xs sm:text-sm shadow-[3px_3px_0_#4D0570] border-2',
        'lg' => 'px-6 py-2.5 sm:px-8 sm:py-3.5 text-base sm:text-lg md:text-xl shadow-[4px_4px_0_#4D0570] border-3 sm:border-4',
        default => 'px-5 py-2 text-sm sm:text-base shadow-[4px_4px_0_#4D0570] border-3',
    };

    $variantStyles = match ($variant) {
        'secondary' => 'bg-hf-pink-500 text-white border-hf-purple-900 hover:bg-hf-pink-400 active:bg-hf-pink-500',
        'ghost' => 'bg-transparent text-hf-yellow-400 border-3 border-hf-yellow-400 hover:bg-hf-yellow-400 hover:text-hf-purple-900 !shadow-none',
        'dark' => 'bg-hf-purple-900 text-hf-yellow-400 border-hf-purple-900 hover:bg-hf-purple-700 active:bg-hf-purple-900',
        default => 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-900 hover:bg-hf-yellow-500 active:bg-hf-yellow-400',
    };

    $classes = "$baseStyles $sizeStyles $variantStyles";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
