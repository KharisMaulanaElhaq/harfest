@props([
    'type' => 'blob', // blob, globe, retro, player
    'size' => 'md',   // sm (80px), md (112px), lg (144px), xl (160px)
    'animate' => true,
])

@php
    $src = match ($type) {
        'retro', 'player' => asset('images/brand/player.png'),
        default => asset('images/brand/globe.png'),
    };

    $alt = match ($type) {
        'retro', 'player' => 'Maskot Resmi Cassette Player Pink Harfest',
        default => 'Maskot Resmi Globe Biru Harfest',
    };

    $sizeClasses = match ($size) {
        'sm' => 'w-20 h-20',
        'lg' => 'w-32 h-32 md:w-36 md:h-36',
        'xl' => 'w-36 h-36 md:w-40 md:h-40',
        default => 'w-24 h-24 md:w-28 md:h-28',
    };

    $animClass = $animate ? ($type === 'retro' || $type === 'player' ? 'animate-float-reverse' : 'animate-float') : '';
@endphp

<img src="{{ $src }}" 
     alt="{{ $alt }}" 
     {{ $attributes->merge(['class' => "$sizeClasses $animClass object-contain select-none pointer-events-none drop-shadow-[4px_4px_0_#4D0570]"]) }}>
