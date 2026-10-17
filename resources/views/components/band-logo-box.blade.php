@props([
    'band',
    'index' => 0,
])

@php
    $isYellow = ($index % 2) === 0;
    $bgClass = $isYellow ? 'bg-hf-yellow-400 text-hf-purple-900' : 'bg-hf-pink-500 text-white';
@endphp

<div class="rounded-3xl border-4 border-hf-purple-900 {{ $bgClass }} shadow-hard p-4 sm:p-5 flex flex-col items-center justify-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-hard-lg group select-none">
    @if ($band->logo)
        <div class="h-14 sm:h-16 flex items-center justify-center mb-2">
            <img src="{{ asset('storage/' . $band->logo) }}" alt="{{ $band->name }}" class="max-h-full max-w-full object-contain filter drop-shadow-[2px_2px_0_#4D0570]">
        </div>
    @else
        <div class="h-14 sm:h-16 flex items-center justify-center mb-2">
            @if ($isYellow)
                <span class="font-sans font-black text-xl sm:text-2xl lg:text-3xl tracking-wide uppercase leading-none text-hf-purple-900 drop-shadow-[2px_2px_0_#DE037A]">
                    {{ $band->name }}
                </span>
            @else
                <span class="font-sans font-black text-xl sm:text-2xl lg:text-3xl tracking-normal uppercase leading-none text-white text-stroke-purple drop-shadow-[2px_2px_0_#4D0570]">
                    {{ $band->name }}
                </span>
            @endif
        </div>
    @endif

    <div class="w-full border-t-2 {{ $isYellow ? 'border-hf-purple-900/40' : 'border-white/40' }} pt-2 mt-1">
        <span class="font-sans font-black text-xs sm:text-sm uppercase tracking-wider block">
            {{ $band->name }}
        </span>
        @if ($band->instagram_url)
            <a href="{{ $band->instagram_url }}" target="_blank" rel="noopener noreferrer" 
               class="text-[11px] font-sans font-bold opacity-80 hover:opacity-100 hover:underline block truncate mt-0.5">
                {{ str_replace('https://instagram.com/', '@', rtrim($band->instagram_url, '/')) }}
            </a>
        @endif
    </div>
</div>
