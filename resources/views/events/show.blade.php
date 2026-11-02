<x-app-layout>
    <x-slot:title>{{ $event->name }} - Harfest</x-slot:title>

    <!-- 1. EVENT HERO HEADER -->
    <section class="relative bg-hf-pattern pt-8 pb-14 sm:pt-12 sm:pb-20 px-4 sm:px-6 lg:px-8 border-b-4 border-hf-purple-900 overflow-hidden">
        <div class="max-w-5xl mx-auto relative z-10 text-center flex flex-col items-center">
            
            <!-- Badges Bar -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                <span class="px-3.5 py-1 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase tracking-wider rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                    VOL {{ $event->edition }}
                </span>
                <x-status-badge :status="$event->status" />
            </div>

            <!-- Title & Tagline -->
            <h1 class="font-sans font-black text-3xl sm:text-5xl md:text-6xl text-white tracking-tight uppercase leading-tight text-stroke-purple drop-shadow-[4px_4px_0_#4D0570] max-w-4xl">
                {{ $event->name }}
            </h1>

            @if ($event->tagline)
                <p class="font-sans font-extrabold text-base sm:text-xl text-hf-yellow-400 mt-2 uppercase tracking-wide">
                    {{ $event->tagline }}
                </p>
            @endif

            <!-- Event Date & Venue Pills -->
            <div class="mt-6 flex flex-wrap justify-center items-center gap-2.5 sm:gap-3">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl shadow-hard text-white font-sans text-xs sm:text-sm font-extrabold">
                    <svg class="w-4 h-4 text-hf-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>{{ format_wib($event->event_date, 'l, d F Y - H:i') }}</span>
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-2 bg-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl shadow-hard text-white font-sans text-xs sm:text-sm font-extrabold">
                    <svg class="w-4 h-4 text-hf-pink-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span>{{ $event->venue }}</span>
                </div>
            </div>

            <!-- Description -->
            <p class="text-sm sm:text-base text-pink-100 font-sans font-medium max-w-2xl mx-auto mt-6 leading-relaxed">
                {{ $event->description }}
            </p>

            <!-- CTA or Closed Notice -->
            <div class="mt-8">
                @if ($isSalesOpen)
                    <x-button href="#tiket" variant="primary" size="lg" class="group">
                        <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>
                        <span>Beli Tiket Sekarang</span>
                    </x-button>
                @else
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-hf-purple-900/90 text-white rounded-2xl border-3 border-hf-purple-900 shadow-hard font-sans font-black text-sm uppercase">
                        <span class="text-lg">🔒</span>
                        <span>Penjualan tiket sudah ditutup untuk edisi ini</span>
                    </div>
                @endif
            </div>

        </div>
    </section>

    <!-- 2. HEADLINER SECTION -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <x-section-title subtitle="PANGGUNG UTAMA" plate="true">
            BINTANG TAMU UTAMA
        </x-section-title>

        <x-headliner :headliners="$headliners" />
    </section>

    <!-- Pattern Divider -->
    <x-pattern-divider variant="yellow" />

    <!-- 3. LINEUP SECTION -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <x-section-title subtitle="LINEUP LENGKAP" plate="true">
            DERETAN MUSISI & BAND
        </x-section-title>

        @if ($regularBands->isNotEmpty())
            <!-- Photo Cards Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 my-8 text-left">
                @foreach ($regularBands as $index => $band)
                    <x-band-frame 
                        :name="$band->name"
                        :description="$band->description"
                        :photo="$band->photo"
                        :order="$index + 1"
                    />
                @endforeach
            </div>

            <!-- Alternating Yellow/Pink Logo Boxes -->
            <div class="mt-12 pt-8 border-t-2 border-white/20">
                <span class="text-xs font-black uppercase tracking-widest text-hf-yellow-400 block text-center mb-6">
                    ✦ LINEUP LOGO SPOTLIGHT ✦
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($regularBands as $idx => $band)
                        <x-band-logo-box :band="$band" :index="$idx" />
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-8 rounded-3xl border-4 border-hf-purple-900 bg-white/10 text-center text-white font-sans my-8">
                <p class="text-base font-bold">Lineup band untuk edisi ini akan segera diumumkan.</p>
            </div>
        @endif
    </section>

    <!-- Pattern Divider -->
    <x-pattern-divider variant="pink" />

    <!-- 4. TIKET SECTION -->
    <section id="tiket" class="py-12 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <x-section-title subtitle="INFORMASI TIKET" plate="true">
            KATEGORI & HARGA TIKET
        </x-section-title>

        @if ($isSalesOpen)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch mt-8">
                @forelse ($ticketTypes as $type)
                    @php
                        $remaining = $type->remaining_quota;
                        $isSoldOut = $remaining <= 0;
                        $isCivitas = $type->requires_student_id;
                        $isBundling = $type->people_per_ticket > 1;
                    @endphp

                    <div class="flex flex-col h-full rounded-3xl border-4 border-hf-purple-900 bg-white text-hf-purple-900 shadow-hard-lg overflow-hidden relative">
                        <div class="p-4 {{ $isCivitas ? 'bg-hf-pink-500 text-white' : ($isBundling ? 'bg-hf-yellow-400 text-hf-purple-900' : 'bg-hf-purple-900 text-hf-yellow-400') }} border-b-3 border-hf-purple-900 flex justify-between items-center">
                            <span class="font-sans font-black text-sm tracking-wider uppercase">
                                {{ $isCivitas ? 'KHUSUS CIVITAS' : ($isBundling ? 'BUNDLING' : 'UMUM') }}
                            </span>
                            @if ($isSoldOut)
                                <span class="px-2.5 py-0.5 bg-rose-600 text-white text-xs font-bold rounded-lg border border-hf-purple-900">HABIS</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-white/20 text-xs font-sans font-black rounded-lg">Sisa: {{ $remaining }}</span>
                            @endif
                        </div>

                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="font-sans font-black text-2xl text-hf-purple-900 uppercase tracking-tight leading-tight mb-2">
                                    {{ $type->name }}
                                </h3>

                                <div class="my-4">
                                    <div class="inline-flex items-baseline gap-1">
                                        <span class="text-sm font-sans font-black text-gray-500">Rp</span>
                                        <span class="font-sans font-black text-4xl text-hf-purple-900 tracking-tight">
                                            {{ number_format($type->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 font-sans mt-0.5 font-semibold">
                                        Berlaku untuk: <strong>{{ $type->people_per_ticket }} orang</strong>
                                    </p>
                                </div>

                                <div class="p-3 bg-purple-50 rounded-2xl border-2 border-dashed border-purple-300 text-xs text-gray-700 font-sans leading-relaxed my-3">
                                    {{ $type->description }}
                                </div>
                            </div>

                            <div class="mt-4">
                                @if ($isSoldOut)
                                    <button disabled class="w-full py-3 bg-gray-300 text-gray-500 font-sans font-black text-sm uppercase rounded-2xl border-3 border-gray-400 cursor-not-allowed">
                                        Tiket Habis
                                    </button>
                                @else
                                    <x-button href="{{ route('orders.create', $type->id) }}" variant="primary" class="w-full">
                                        Beli Tiket Ini
                                    </x-button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-white font-sans">
                        <p>Belum ada tiket yang terdaftar untuk event ini.</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="p-8 sm:p-12 rounded-3xl border-4 border-hf-purple-900 bg-hf-purple-900/80 text-center text-white font-sans max-w-2xl mx-auto my-8">
                <span class="text-4xl block mb-3">🎫</span>
                <h4 class="font-sans font-black text-xl sm:text-2xl text-hf-yellow-400 uppercase tracking-tight mb-2">
                    Penjualan sudah ditutup
                </h4>
                <p class="text-xs sm:text-sm text-purple-200 font-medium leading-relaxed">
                    Edisi ini telah selesai diselenggarakan atau waktu penjualan tiket telah berakhir. Pantau edisi aktif Harfest lainnya!
                </p>
                <div class="mt-6">
                    <a href="{{ route('events.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-sm hover:bg-hf-yellow-500">
                        <span>Lihat Edisi Lain</span>
                    </a>
                </div>
            </div>
        @endif
    </section>
</x-app-layout>
