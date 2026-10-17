@props([
    'name' => '',
    'description' => '',
    'photo' => null,
    'order' => 1,
])

@php
    // Consistent placeholder silhouettes for bands if no image uploaded
    $dummySvgPlaceholders = [
        1 => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300" fill="%232D0A4E"><rect width="400" height="300" fill="%233A1160"/><circle cx="120" cy="110" r="35" fill="%23A5B4FC"/><path d="M70 200 C70 150 170 150 170 200 Z" fill="%23A5B4FC"/><circle cx="210" cy="90" r="35" fill="%23E0E7FF"/><path d="M160 200 C160 140 260 140 260 200 Z" fill="%23E0E7FF"/><circle cx="300" cy="115" r="35" fill="%23C7D2FE"/><path d="M250 200 C250 155 350 155 350 200 Z" fill="%23C7D2FE"/></svg>',
        2 => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300" fill="%232D0A4E"><rect width="400" height="300" fill="%234A0E4E"/><circle cx="150" cy="100" r="40" fill="%23FBCFE8"/><path d="M90 210 C90 140 210 140 210 210 Z" fill="%23FBCFE8"/><circle cx="270" cy="110" r="38" fill="%23F472B6"/><path d="M210 210 C210 150 330 150 330 210 Z" fill="%23F472B6"/></svg>',
        3 => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300" fill="%232D0A4E"><rect width="400" height="300" fill="%231E1B4B"/><circle cx="130" cy="105" r="36" fill="%2393C5FD"/><path d="M80 205 C80 150 180 150 180 205 Z" fill="%2393C5FD"/><circle cx="270" cy="105" r="36" fill="%2360A5FA"/><path d="M220 205 C220 150 320 150 320 205 Z" fill="%2360A5FA"/></svg>',
        4 => 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300" fill="%232D0A4E"><rect width="400" height="300" fill="%23312E81"/><circle cx="110" cy="110" r="32" fill="%23FDE047"/><path d="M65 200 C65 150 155 150 155 200 Z" fill="%23FDE047"/><circle cx="200" cy="95" r="34" fill="%23FEF08A"/><path d="M155 200 C155 140 245 140 245 200 Z" fill="%23FEF08A"/><circle cx="290" cy="110" r="32" fill="%23FACC15"/><path d="M245 200 C245 150 335 150 335 200 Z" fill="%23FACC15"/></svg>',
    ];

    $imageSrc = $photo ? asset('storage/' . $photo) : ($dummySvgPlaceholders[$order % 4 ?: 4]);
@endphp

<div class="group relative flex flex-col items-center w-full">
    <!-- Thick Outer Frame (Consistent across all cards: thick border-4 border-hf-purple-900 with hot-pink inner border) -->
    <div class="w-full p-2.5 sm:p-3 rounded-3xl border-4 border-hf-purple-900 bg-hf-purple-900 shadow-hard transition-all duration-300 group-hover:shadow-hard-lg group-hover:-translate-y-1.5">
        <!-- Inner Photo Container (Grayscale with thick pink IG feed frame) -->
        <div class="relative w-full aspect-[4/3] rounded-2xl overflow-hidden border-4 border-hf-pink-500 bg-black">
            <img src="{{ $imageSrc }}" 
                 alt="{{ $name }}" 
                 class="w-full h-full object-cover band-grayscale-filter group-hover:scale-105 transition-transform duration-300">
            
            <!-- Vignette Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20 pointer-events-none"></div>

            <!-- Stylized Band Name on Image Bottom (Break-words, never cut off) -->
            <div class="absolute bottom-1.5 inset-x-1.5 sm:bottom-2 sm:inset-x-2 text-center pointer-events-none">
                <span class="font-sans font-black text-xs sm:text-base md:text-lg lg:text-xl tracking-wider text-white text-stroke-purple uppercase drop-shadow-[2px_2px_0_#4D0570] break-words leading-tight block px-1">
                    {{ $name }}
                </span>
            </div>
        </div>
    </div>

    <!-- Description Card Snippet below frame (Clean, full sentence display without awkward mid-sentence cutoffs) -->
    @if ($description)
        <div class="mt-2.5 px-1 sm:px-2 text-center w-full">
            <p class="text-xs sm:text-sm text-pink-100 font-sans leading-relaxed font-semibold">
                {{ $description }}
            </p>
        </div>
    @endif
</div>
