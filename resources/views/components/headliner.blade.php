@props([
    'headliners' => collect(),
])

@php
    $hasHeadliner = $headliners instanceof \Illuminate\Support\Collection ? $headliners->isNotEmpty() : !empty($headliners);
@endphp

<div class="relative w-full max-w-4xl mx-auto my-8">
    @if ($hasHeadliner)
        @foreach ($headliners as $headliner)
            <div class="relative rounded-3xl border-4 border-hf-yellow-400 bg-hf-purple-900 p-6 sm:p-8 shadow-hard-lg overflow-hidden group">
                <!-- Background Glow -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-hf-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Floating Mascot Left (Globe) -->
                <div class="hidden sm:block absolute -left-6 top-1/2 -translate-y-1/2 z-20 pointer-events-none">
                    <img src="{{ asset('images/brand/globe.png') }}" alt="Globe" class="w-20 md:w-28 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
                </div>

                <!-- Floating Mascot Right (Player) -->
                <div class="hidden sm:block absolute -right-6 top-1/2 -translate-y-1/2 z-20 pointer-events-none">
                    <img src="{{ asset('images/brand/player.png') }}" alt="Player" class="w-16 md:w-24 h-auto object-contain animate-float-reverse drop-shadow-[4px_4px_0_#4D0570]">
                </div>

                <div class="flex flex-col md:flex-row items-center gap-6 relative z-10 text-center md:text-left">
                    <!-- Photo Cutout with Yellow/Pink Outline -->
                    <div class="shrink-0 relative">
                        <div class="w-36 h-36 sm:w-44 sm:h-44 md:w-48 md:h-48 rounded-3xl border-4 border-hf-pink-500 p-1.5 bg-hf-purple-950 shadow-hard transform -rotate-2 group-hover:rotate-0 transition-transform duration-300">
                            @if ($headliner->photo)
                                <img src="{{ asset('storage/' . $headliner->photo) }}" 
                                     alt="{{ $headliner->name }}" 
                                     class="w-full h-full object-cover rounded-2xl">
                            @else
                                <div class="w-full h-full rounded-2xl bg-gradient-to-br from-hf-pink-500 via-hf-purple-900 to-hf-purple-950 flex items-center justify-center">
                                    <span class="font-sans font-black text-4xl sm:text-5xl text-hf-yellow-400 text-stroke-purple drop-shadow">
                                        {{ substr($headliner->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <!-- Sparkle sticker -->
                        <div class="absolute -top-3 -right-2 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-[10px] px-2.5 py-0.5 rounded-full border-2 border-hf-purple-900 shadow-hard-sm uppercase transform rotate-6">
                            ★ Headliner
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="flex-grow space-y-3">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-hf-pink-500 text-white font-sans font-black text-xs uppercase tracking-wider rounded-full border border-hf-purple-900">
                            <span>🔥 Bintang Tamu Utama</span>
                        </div>

                        <h3 class="font-sans font-black text-3xl sm:text-4xl lg:text-5xl text-hf-yellow-400 tracking-tight uppercase leading-tight">
                            {{ $headliner->name }}
                        </h3>

                        <p class="text-sm sm:text-base text-pink-100 font-sans font-medium leading-relaxed max-w-xl">
                            {{ $headliner->description }}
                        </p>

                        @if ($headliner->instagram_url)
                            <div class="pt-2">
                                <a href="{{ $headliner->instagram_url }}" target="_blank" rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-2 text-xs sm:text-sm font-sans font-black text-white hover:text-hf-yellow-400 transition-colors">
                                    <svg class="w-4 h-4 fill-current text-hf-yellow-400" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    <span>{{ str_replace('https://instagram.com/', '@', rtrim($headliner->instagram_url, '/')) }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Mystery "Akan Diumumkan" Card -->
        <div class="relative rounded-3xl border-4 border-dashed border-hf-yellow-400 bg-hf-purple-900/90 p-6 sm:p-10 shadow-hard-lg text-center overflow-hidden">
            <!-- Floating Mascot Left -->
            <div class="hidden sm:block absolute left-4 top-1/2 -translate-y-1/2 z-20 pointer-events-none">
                <img src="{{ asset('images/brand/globe.png') }}" alt="Globe" class="w-20 md:w-24 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
            </div>

            <!-- Floating Mascot Right -->
            <div class="hidden sm:block absolute right-4 top-1/2 -translate-y-1/2 z-20 pointer-events-none">
                <img src="{{ asset('images/brand/player.png') }}" alt="Player" class="w-16 md:w-20 h-auto object-contain animate-float-reverse drop-shadow-[4px_4px_0_#4D0570]">
            </div>

            <div class="relative z-10 max-w-md mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase tracking-wider rounded-full border-2 border-hf-purple-900 shadow-hard-sm">
                    <span>✨ BINTANG TAMU UTAMA</span>
                </div>

                <div class="w-24 h-24 mx-auto rounded-3xl bg-hf-purple-950 border-3 border-hf-pink-500 flex items-center justify-center shadow-hard">
                    <span class="font-sans font-black text-5xl text-hf-yellow-400 animate-pulse">?</span>
                </div>

                <h3 class="font-sans font-black text-2xl sm:text-3xl md:text-4xl text-white tracking-tight uppercase">
                    SEGERA DIUMUMKAN!
                </h3>

                <p class="text-xs sm:text-sm text-pink-200 font-sans font-semibold leading-relaxed">
                    Siap-siap dibuat kagum! Musisi nasional papan atas siap memeriahkan panggung utama Harfest 2026. Pantau terus update selanjutnya!
                </p>
            </div>
        </div>
    @endif
</div>
