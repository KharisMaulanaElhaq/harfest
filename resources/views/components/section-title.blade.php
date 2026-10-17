@props([
    'subtitle' => null,
    'align' => 'center',
    'rotate' => '-rotate-1',
    'plate' => true,
])

@php
    $alignClass = match ($align) {
        'left' => 'text-left items-start',
        'right' => 'text-right items-end',
        default => 'text-center items-center',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col $alignClass my-6"]) }}>
    @if ($subtitle)
        <div class="inline-flex items-center gap-1.5 px-4 py-1 mb-2.5 text-xs sm:text-sm font-black tracking-widest text-white uppercase bg-hf-pink-500 border-2 border-hf-purple-900 rounded-full shadow-hard-sm transform -rotate-1 select-none">
            <svg class="w-3.5 h-3.5 fill-current text-hf-yellow-400" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            <span>{{ $subtitle }}</span>
            <svg class="w-3.5 h-3.5 fill-current text-hf-yellow-400" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
        </div>
    @endif

    @if ($plate)
        <!-- Yellow Plate with Purple Outline & Drop Shadow (Like Logo Plate in Banner) -->
        <div class="relative inline-block px-6 py-2.5 sm:px-8 sm:py-3.5 bg-hf-yellow-400 border-4 border-hf-purple-900 rounded-2xl sm:rounded-3xl shadow-hard transform {{ $rotate }} transition-transform duration-300 hover:scale-105">
            <!-- Lightning spark accent on top-right -->
            <div class="absolute -top-3.5 -right-3.5 text-hf-blue-400">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-current drop-shadow-[2px_2px_0_#4D0570]" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            </div>
            
            <h2 class="font-sans font-black text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-hf-purple-900 tracking-tight uppercase leading-none select-none">
                {{ $slot }}
            </h2>
        </div>
    @else
        <!-- Text with Purple Outline Stroke -->
        <h2 class="font-sans font-black text-3xl sm:text-4xl md:text-5xl lg:text-6xl text-hf-yellow-400 text-stroke-purple-thick tracking-tight transform {{ $rotate }} drop-shadow-[4px_4px_0_#4D0570] transition-transform duration-300 hover:scale-105 inline-block uppercase leading-tight select-none">
            {{ $slot }}
        </h2>
    @endif
</div>
