<x-app-layout>
    <x-slot:title>Harfest 2026 - Harkat Negeri Fest</x-slot:title>

    <!-- 1. HERO SECTION (Magenta #9A0474 + Logo Transparan Resmi Tanpa Crop) -->
    <section class="relative bg-hf-pattern pt-3 pb-10 sm:pt-10 sm:pb-24 px-4 sm:px-6 lg:px-8 border-b-4 border-hf-purple-900 overflow-hidden">
        <!-- Background Vignette Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/15 via-transparent to-black/25 pointer-events-none"></div>

        <!-- Decorative Floating Mascots in Hero Corners (Absolute, outside text area, z-0) -->
        <div class="hidden lg:block absolute left-4 xl:left-8 top-16 z-0 pointer-events-none opacity-85">
            <img src="{{ asset('images/brand/globe.png') }}" 
                 alt="Globe Mascot" 
                 class="w-28 md:w-36 xl:w-44 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
        </div>
        <div class="hidden lg:block absolute right-4 xl:right-8 top-20 z-0 pointer-events-none opacity-85">
            <img src="{{ asset('images/brand/player.png') }}" 
                 alt="Player Mascot" 
                 class="w-24 md:w-32 xl:w-36 h-auto object-contain animate-float-reverse drop-shadow-[4px_4px_0_#4D0570]">
        </div>

        <div class="max-w-6xl mx-auto relative z-10 text-center flex flex-col items-center">
            
            <!-- Top Announcement Sticker Ribbon with Lightning Sparks (Short single line) -->
            <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3.5 py-1 sm:px-4 sm:py-1.5 mb-2.5 sm:mb-6 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs sm:text-sm tracking-wider uppercase border-2 sm:border-3 border-hf-purple-900 rounded-full shadow-hard-sm transform -rotate-1 select-none whitespace-nowrap">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-current text-hf-blue-400 flex-shrink-0" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
                <span>FESTIVAL MUSIK KAMPUS 2026</span>
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-current text-hf-blue-400 flex-shrink-0" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- OFFICIAL TRANSPARENT LOGO (Directly on Magenta Background, Compact Responsive) -->
            <div class="w-full max-w-4xl mx-auto flex justify-center items-center my-1 sm:my-3 px-2 sm:px-4 relative z-10">
                <img src="{{ asset('images/brand/harfest-logo.png') }}" 
                     alt="Harfest 2026 - Harkat Negeri Fest" 
                     class="w-full max-w-[270px] sm:max-w-3xl md:max-w-4xl h-auto object-contain select-none transform hover:scale-[1.01] transition-transform duration-300 drop-shadow-[4px_4px_0_#4D0570]">
            </div>

            <!-- Event Details Pill (Date & Venue from Database, Compact on Mobile) -->
            <div class="mt-2.5 sm:mt-6 flex flex-wrap justify-center items-center gap-2 sm:gap-3 relative z-10">
                <!-- Date Pill -->
                <div class="inline-flex items-center gap-1.5 sm:gap-2.5 px-3.5 py-1.5 sm:px-5 sm:py-2.5 bg-hf-purple-900 border-2 sm:border-3 border-hf-purple-900 rounded-xl sm:rounded-2xl shadow-[3px_3px_0_#4D0570] sm:shadow-hard text-white font-sans text-xs sm:text-base font-extrabold">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-hf-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>{{ $event ? $event->event_date->locale('id')->translatedFormat('l, d F Y') : 'Sabtu, 28 November 2026' }}</span>
                </div>

                <!-- Venue Pill -->
                <div class="inline-flex items-center gap-1.5 sm:gap-2.5 px-3.5 py-1.5 sm:px-5 sm:py-2.5 bg-hf-purple-900 border-2 sm:border-3 border-hf-purple-900 rounded-xl sm:rounded-2xl shadow-[3px_3px_0_#4D0570] sm:shadow-hard text-white font-sans text-xs sm:text-base font-extrabold">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-hf-pink-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>{{ $event ? $event->venue : 'Lapangan Utama Universitas Harkat Negeri' }}</span>
                </div>
            </div>

            <!-- Action CTA Buttons (Tight vertical margin, 4px shadow, clean outline) -->
            <div class="mt-3.5 sm:mt-8 flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-4 w-full sm:w-auto relative z-10">
                <!-- Primary Buy Button (Kuning, Teks Ungu, Offset 4px) -->
                <x-button href="#tiket" variant="primary" size="lg" class="w-full sm:w-auto group">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-1.5 sm:mr-2 text-hf-purple-900 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.6 11.025a.75.75 0 00-.914.593 8.462 8.462 0 001.077 5.86 8.441 8.441 0 004.832 3.82c.404.116.828.175 1.255.175a8.437 8.437 0 006.012-2.529 8.47 8.47 0 002.39-5.998c0-3.327-1.921-6.208-4.707-7.61a.75.75 0 00-.986.345 5.962 5.962 0 01-2.597 2.602zM12 10.5a3.75 3.75 0 00-3.75 3.75c0 .647.164 1.256.452 1.788a.75.75 0 001.298-.75A2.25 2.25 0 019.75 14.25a2.25 2.25 0 014.5 0c0 .388-.098.753-.272 1.072a.75.75 0 101.32.716c.45-.828.702-1.78.702-2.788A3.75 3.75 0 0012 10.5z" clip-rule="evenodd" />
                    </svg>
                    <span>Beli Tiket Sekarang</span>
                </x-button>

                <!-- Outline Button (Kuning dengan Border Tanpa Bayangan) -->
                <x-button href="#lineup" variant="ghost" size="lg" class="w-full sm:w-auto group">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-1.5 sm:mr-2 text-hf-yellow-400 group-hover:text-hf-purple-900 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Lineup Bintang Tamu</span>
                </x-button>
            </div>

            <!-- Price Quick Bar (Label Kuning #FFD000 untuk kontras tinggi) -->
            <div class="mt-5 sm:mt-10 pt-3 sm:pt-6 border-t-2 border-hf-purple-900/40 w-full max-w-2xl flex flex-wrap justify-center items-center gap-2.5 sm:gap-4 relative z-10">
                <span class="text-xs font-black uppercase tracking-widest text-hf-yellow-400 w-full text-center mb-1 drop-shadow-sm">Pilihan Tiket:</span>
                @forelse ($ticketTypes as $tIdx => $tQuick)
                    @php
                        $priceStr = $tQuick->price >= 1000 ? ($tQuick->price / 1000) . 'K' : $tQuick->price;
                        $variant = $tIdx % 2 === 0 ? 'pink' : 'yellow';
                    @endphp
                    <x-price-pill :label="$tQuick->name" :price="$priceStr" :variant="$variant" size="sm" />
                @empty
                    <x-price-pill label="Civitas Harkat Negeri" price="30K" variant="pink" size="sm" />
                    <x-price-pill label="Tiket Umum 1 Orang" price="40K" variant="yellow" size="sm" />
                    <x-price-pill label="Tiket Umum 3 Orang" price="100K" variant="pink" size="sm" />
                @endforelse
            </div>

        </div>
    </section>

    <!-- DECORATIVE DIVIDER 1 -->
    <x-pattern-divider variant="pink" />

    <!-- 2. LINEUP SECTION (Subtle Tone-on-tone Magenta Pattern matching Banner) -->
    <section id="lineup" class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 bg-hf-pattern relative border-b-4 border-hf-purple-900 overflow-hidden">
        <!-- Floating Mascot at Corner of Lineup (Outside text area, z-0) -->
        <div class="hidden lg:block absolute right-6 top-8 z-0 pointer-events-none opacity-80">
            <img src="{{ asset('images/brand/player.png') }}" alt="Player Mascot" class="w-32 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
        </div>

        <div class="max-w-6xl mx-auto text-center relative z-10">
            
            <x-section-title subtitle="PANGGUNG UTAMA" plate="true">
                LINEUP BINTANG TAMU
            </x-section-title>

            <p class="text-center text-sm sm:text-base md:text-lg text-pink-100 font-sans max-w-xl mx-auto mb-8 font-medium">
                Saksikan penampilan spektakuler para musisi dan band indie kampus terpopuler yang siap menghentak panggung Harfest!
            </p>

            <!-- Bintang Tamu Utama (Headliner) -->
            <x-headliner :headliners="$headliners ?? ($event ? $event->headliners : collect())" />

            <!-- Band Grid: 2 columns in mobile (375px), 4 columns in desktop -->
            @php
                $displayBands = ($regularBands ?? null) && $regularBands->isNotEmpty() 
                    ? $regularBands 
                    : ($event ? $event->regularBands : $bands);
                if ($displayBands->isEmpty() && $bands->isNotEmpty()) {
                    $displayBands = $bands;
                }
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 text-left mt-8">
                @forelse ($displayBands as $index => $band)
                    <x-band-frame 
                        :name="$band->name"
                        :description="$band->description"
                        :photo="$band->photo"
                        :order="$index + 1"
                    />
                @empty
                    <div class="col-span-full py-12 text-center text-white font-sans">
                        <p class="text-lg">Lineup band akan segera diumumkan! Pantau terus informasi selanjutnya.</p>
                    </div>
                @endforelse
            </div>

            <!-- Instagram Feed Reference Button -->
            <div class="mt-12 text-center">
                <a href="https://instagram.com/harkatnegerifest" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2.5 px-6 py-3 bg-hf-purple-900 text-white font-sans font-extrabold text-sm sm:text-base tracking-wide rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press hover:bg-hf-pink-500 transition-all">
                    <svg class="w-5 h-5 fill-current text-hf-yellow-400" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span>Follow Instagram @harkatnegerifest</span>
                </a>
            </div>

        </div>
    </section>

    <!-- DECORATIVE DIVIDER 2 -->
    <x-pattern-divider variant="yellow" />

    <!-- 3. HARGA & KATEGORI TIKET (Magenta #9A0474 Dominan) -->
    <section id="tiket" class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 bg-hf-pattern relative border-b-4 border-hf-purple-900 overflow-hidden">
        <!-- Floating Mascot at Corner of Tiket (Outside text area, z-0) -->
        <div class="hidden lg:block absolute left-6 top-10 z-0 pointer-events-none opacity-80">
            <img src="{{ asset('images/brand/globe.png') }}" alt="Globe Mascot" class="w-32 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
        </div>

        <div class="max-w-6xl mx-auto relative z-10">
            
            <x-section-title subtitle="KATEGORI TIKET" plate="true">
                DAFTAR HARGA TIKET
            </x-section-title>

            <p class="text-center text-sm sm:text-base text-pink-100 font-sans max-w-xl mx-auto mb-12 font-medium">
                Pilih kategori tiket yang sesuai. Pembayaran mudah via transfer bank/QRIS manual dan langsung diverifikasi admin!
            </p>

            <!-- 3 Ticket Cards Grid (Dynamic Prices from Database) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">
                @forelse ($ticketTypes as $index => $type)
                    @php
                        $remaining = $type->remaining_quota;
                        $isSoldOut = $remaining <= 0;
                        $isCivitas = $type->requires_student_id;
                        $isBundling = $type->people_per_ticket > 1;
                    @endphp

                    <div class="flex flex-col h-full rounded-3xl border-4 border-hf-purple-900 bg-white text-hf-purple-900 shadow-hard-lg overflow-hidden relative transition-all duration-200 hover:-translate-y-1.5 hover:shadow-[10px_10px_0_#4D0570]">
                        
                        <!-- Top Header Color Banner -->
                        <div class="p-4 {{ $isCivitas ? 'bg-hf-pink-500 text-white' : ($isBundling ? 'bg-hf-yellow-400 text-hf-purple-900' : 'bg-hf-purple-900 text-hf-yellow-400') }} border-b-3 border-hf-purple-900 flex justify-between items-center">
                            <span class="font-sans font-black text-sm tracking-wider uppercase">
                                {{ $isCivitas ? 'KHUSUS CIVITAS' : ($isBundling ? 'PAKET BUNDLING 3 ORANG' : 'TIKET UMUM') }}
                            </span>

                            @if ($isSoldOut)
                                <span class="px-2.5 py-0.5 bg-rose-600 text-white text-xs font-bold rounded-lg border border-hf-purple-900">
                                    HABIS
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-white/20 text-xs font-sans font-black rounded-lg">
                                    Sisa: {{ $remaining }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <!-- Ticket Name -->
                                <h3 class="font-sans font-black text-2xl text-hf-purple-900 uppercase tracking-tight leading-tight mb-2">
                                    {{ $type->name }}
                                </h3>

                                <!-- Price Display (Server Database Value) -->
                                <div class="my-4">
                                    <div class="inline-flex items-baseline gap-1">
                                        <span class="text-sm font-sans font-black text-gray-500">Rp</span>
                                        <span class="font-sans font-black text-4xl sm:text-5xl text-hf-purple-900 tracking-tight">
                                            {{ number_format($type->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 font-sans mt-0.5 font-semibold">
                                        Berlaku untuk: <strong>{{ $type->people_per_ticket }} orang</strong>
                                    </p>
                                </div>

                                <!-- Description -->
                                <div class="p-3 bg-purple-50 rounded-2xl border-2 border-dashed border-purple-300 text-xs sm:text-sm text-gray-700 font-sans leading-relaxed my-4">
                                    {{ $type->description }}
                                </div>

                                <!-- Requirements bullet points with SVG icons -->
                                <ul class="space-y-2 text-xs sm:text-sm font-sans text-gray-700 my-4 font-semibold">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span>E-Tiket QR resmi per orang</span>
                                    </li>
                                    @if ($isCivitas)
                                        <li class="flex items-center gap-2 text-hf-pink-500 font-bold">
                                            <svg class="w-4 h-4 text-hf-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                            <span>Wajib upload foto KTM / Kartu Pegawai</span>
                                        </li>
                                    @endif
                                    @if ($isBundling)
                                        <li class="flex items-center gap-2 text-hf-purple-900 font-bold">
                                            <svg class="w-4 h-4 text-hf-yellow-500 flex-shrink-0 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span>Hemat dibanding tiket satuan</span>
                                        </li>
                                    @endif
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span>Akses penuh panggung konser</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Buy Button with SVG Icon (Montserrat 800) -->
                            <div class="pt-4 border-t-2 border-gray-100">
                                @if ($isSoldOut)
                                    <button disabled class="w-full py-3.5 bg-gray-300 text-gray-500 border-3 border-gray-400 rounded-2xl font-sans font-bold text-base uppercase tracking-wider cursor-not-allowed select-none">
                                        Tiket Sold Out
                                    </button>
                                @else
                                    @auth
                                        <a href="{{ route('orders.create', $type->id) }}" class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-sans font-extrabold text-base uppercase tracking-wider shadow-hard btn-press hover:bg-hf-yellow-500 transition-all">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 20 20"><path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.6 11.025a.75.75 0 00-.914.593 8.462 8.462 0 001.077 5.86 8.441 8.441 0 004.832 3.82c.404.116.828.175 1.255.175a8.437 8.437 0 006.012-2.529 8.47 8.47 0 002.39-5.998c0-3.327-1.921-6.208-4.707-7.61a.75.75 0 00-.986.345 5.962 5.962 0 01-2.597 2.602zM12 10.5a3.75 3.75 0 00-3.75 3.75c0 .647.164 1.256.452 1.788a.75.75 0 001.298-.75A2.25 2.25 0 019.75 14.25a2.25 2.25 0 014.5 0c0 .388-.098.753-.272 1.072a.75.75 0 101.32.716c.45-.828.702-1.78.702-2.788A3.75 3.75 0 0012 10.5z" clip-rule="evenodd"/></svg>
                                            <span>Pesan Tiket Ini</span>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-sans font-extrabold text-base uppercase tracking-wider shadow-hard btn-press hover:bg-hf-yellow-500 transition-all">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                                            <span>Login untuk Memesan</span>
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-pink-200">
                        <p class="text-lg">Kategori tiket belum dibuka. Nantikan penjualan resminya!</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- DECORATIVE DIVIDER 3 -->
    <x-pattern-divider variant="pink" />

    <!-- 4. TENTANG EVENT (Magenta #9A0474 + Kartu Ungu #4D0570) -->
    <section class="py-14 sm:py-20 px-4 sm:px-6 lg:px-8 bg-hf-pattern relative border-b-4 border-hf-purple-900">
        <div class="max-w-5xl mx-auto">
            <x-card variant="dark" class="text-center relative overflow-hidden bg-hf-purple-900 border-4 border-hf-purple-900 shadow-hard-lg">
                <div class="relative z-10 max-w-3xl mx-auto">
                    <span class="inline-block px-4 py-1.5 mb-3 text-xs font-black tracking-wider text-hf-purple-900 uppercase bg-hf-yellow-400 border-2 border-hf-purple-900 rounded-full shadow-hard-sm">
                        TENTANG HARFEST 2026
                    </span>

                    <h2 class="font-sans font-black text-2xl sm:text-4xl text-hf-yellow-400 uppercase tracking-tight mb-4">
                        Perayaan Musik & Kreativitas Mahasiswa
                    </h2>

                    <p class="font-sans text-sm sm:text-base text-purple-100 leading-relaxed font-medium">
                        {{ $event ? $event->description : 'Harkat Negeri Fest (Harfest) adalah panggung akbar festival musik tahunan persembahan Universitas Harkat Negeri. Menghadirkan deretan band indie kampus pilihan dengan eksplorasi genre musik tanpa batas—dari math-rock, psychedelic pop, midwest emo, hingga funk fusion.' }}
                    </p>

                    <!-- Feature Badges with crisp SVG Icons -->
                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 pt-4 border-t-2 border-hf-purple-700">
                        <div class="p-3 bg-hf-magenta/40 rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col items-center">
                            <svg class="w-7 h-7 text-hf-yellow-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                            <span class="font-sans font-black text-xs sm:text-sm text-hf-yellow-400">4+ Live Band</span>
                        </div>
                        <div class="p-3 bg-hf-magenta/40 rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col items-center">
                            <svg class="w-7 h-7 text-hf-pink-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                            <span class="font-sans font-black text-xs sm:text-sm text-hf-pink-400">Tenant & Kuliner</span>
                        </div>
                        <div class="p-3 bg-hf-magenta/40 rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col items-center">
                            <svg class="w-7 h-7 text-hf-blue-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.562C22 6.5 17.5 2 12 2z"></path></svg>
                            <span class="font-sans font-black text-xs sm:text-sm text-hf-blue-400">Art Showcase</span>
                        </div>
                        <div class="p-3 bg-hf-magenta/40 rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col items-center">
                            <svg class="w-7 h-7 text-hf-yellow-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span class="font-sans font-black text-xs sm:text-sm text-hf-yellow-400">E-Tiket QR Cepat</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </section>

    <!-- DECORATIVE DIVIDER 4 -->
    <x-pattern-divider variant="yellow" />

    <!-- 5. TUTORIAL PEMBELIAN TIKET -->
    <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 bg-hf-pattern text-white relative border-b-4 border-hf-purple-900">
        <div class="max-w-5xl mx-auto">
            
            <x-section-title subtitle="PANDUAN LENGKAP" plate="true">
                TUTORIAL PEMBELIAN TIKET
            </x-section-title>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mt-10">
                <!-- Step 1 -->
                <div class="bg-hf-purple-900 p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard relative flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-2xl rounded-2xl border-2 border-hf-purple-900 flex items-center justify-center shadow-hard-sm mb-3">
                        1
                    </div>
                    <h4 class="font-sans font-black text-lg text-hf-yellow-400 uppercase mb-2">Pilih Tiket</h4>
                    <p class="text-xs text-purple-200 font-sans font-medium">
                        Pilih jenis tiket dan jumlah paket yang diinginkan. Khusus Civitas, siapkan foto KTM.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-hf-purple-900 p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard relative flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-hf-pink-500 text-white font-sans font-black text-2xl rounded-2xl border-2 border-hf-purple-900 flex items-center justify-center shadow-hard-sm mb-3">
                        2
                    </div>
                    <h4 class="font-sans font-black text-lg text-hf-pink-400 uppercase mb-2">Transfer Bayar</h4>
                    <p class="text-xs text-purple-200 font-sans font-medium">
                        Lakukan transfer tepat sesuai nominal ke rekening BCA atau scan QRIS panitia sebelum 24 jam.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-hf-purple-900 p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard relative flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-hf-blue-400 text-hf-purple-900 font-sans font-black text-2xl rounded-2xl border-2 border-hf-purple-900 flex items-center justify-center shadow-hard-sm mb-3">
                        3
                    </div>
                    <h4 class="font-sans font-black text-lg text-hf-blue-400 uppercase mb-2">Upload Bukti</h4>
                    <p class="text-xs text-purple-200 font-sans font-medium">
                        Unggah struk/screenshot bukti transfer di halaman pesanan untuk diverifikasi oleh panitia.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-hf-purple-900 p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard relative flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-emerald-400 text-hf-purple-900 font-sans font-black text-2xl rounded-2xl border-2 border-hf-purple-900 flex items-center justify-center shadow-hard-sm mb-3">
                        4
                    </div>
                    <h4 class="font-sans font-black text-lg text-emerald-400 uppercase mb-2">Terbit E-Tiket</h4>
                    <p class="text-xs text-purple-200 font-sans font-medium">
                        Setelah pembayaran diterima, e-tiket QR terbit otomatis dan siap ditunjukkan di gerbang masuk!
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- DECORATIVE DIVIDER 5 -->
    <x-pattern-divider variant="pink" />

    <!-- 6. FINAL ENERGETIC CTA SECTION (Magenta #9A0474 Dominan dengan Aksen Kuning) -->
    <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 bg-hf-pattern border-t-4 border-hf-purple-900 text-white text-center relative overflow-hidden">
        <!-- Floating Corner Mascots (Absolute, outside text area, z-0) -->
        <div class="hidden sm:block absolute left-6 bottom-6 pointer-events-none opacity-85 z-0">
            <img src="{{ asset('images/brand/globe.png') }}" alt="Globe Mascot" class="w-24 md:w-32 h-auto object-contain animate-float drop-shadow-[4px_4px_0_#4D0570]">
        </div>
        <div class="hidden sm:block absolute right-6 bottom-6 pointer-events-none opacity-85 z-0">
            <img src="{{ asset('images/brand/player.png') }}" alt="Player Mascot" class="w-24 md:w-28 h-auto object-contain animate-float-reverse drop-shadow-[4px_4px_0_#4D0570]">
        </div>

        <div class="max-w-4xl mx-auto relative z-10 flex flex-col items-center">
            <span class="inline-block px-4 py-1.5 mb-4 text-xs font-sans font-black tracking-widest text-hf-purple-900 uppercase bg-hf-yellow-400 border-2 border-hf-purple-900 rounded-full shadow-hard-sm transform -rotate-1">
                JANGAN TUNGGU SAMPAI KEHABISAN
            </span>

            <h2 class="font-sans font-black text-3xl sm:text-5xl md:text-6xl text-white text-stroke-purple-thick uppercase tracking-tight leading-none mb-4 drop-shadow-[4px_4px_0_#4D0570]">
                KUOTA TIKET SANGAT TERBATAS!
            </h2>

            <p class="text-sm sm:text-lg text-pink-100 font-sans max-w-xl mx-auto mb-8 font-semibold">
                Amankan tiketmu sekarang juga dan jadilah bagian dari malam bersejarah festival musik kampus Harkat Negeri Fest 2026!
            </p>

            <x-button href="#tiket" variant="primary" size="lg" class="shadow-hard-lg group">
                <svg class="w-6 h-6 mr-2 text-hf-purple-900 flex-shrink-0 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.6 11.025a.75.75 0 00-.914.593 8.462 8.462 0 001.077 5.86 8.441 8.441 0 004.832 3.82c.404.116.828.175 1.255.175a8.437 8.437 0 006.012-2.529 8.47 8.47 0 002.39-5.998c0-3.327-1.921-6.208-4.707-7.61a.75.75 0 00-.986.345 5.962 5.962 0 01-2.597 2.602zM12 10.5a3.75 3.75 0 00-3.75 3.75c0 .647.164 1.256.452 1.788a.75.75 0 001.298-.75A2.25 2.25 0 019.75 14.25a2.25 2.25 0 014.5 0c0 .388-.098.753-.272 1.072a.75.75 0 101.32.716c.45-.828.702-1.78.702-2.788A3.75 3.75 0 0012 10.5z" clip-rule="evenodd" />
                </svg>
                <span>BELI TIKET SEKARANG</span>
            </x-button>
        </div>
    </section>

</x-app-layout>
