<x-app-layout>
    <x-slot:title>Pilih Tiket - Harfest 2026</x-slot:title>

    <div class="py-12 md:py-20 bg-hf-pattern min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <div class="text-center mb-12">
                <x-section-title subtitle="TIKET RESMI HARFEST 2026">
                    PILIH TIKET KONSERMU!
                </x-section-title>
                <p class="font-sans text-sm sm:text-base text-purple-200 max-w-xl mx-auto mt-2">
                    Dapatkan tiket resmi festival musik kampus Universitas Harkat Negeri. Kuota terbatas, pilih kategori dan amankan sebelum habis!
                </p>
            </div>

            <!-- Tickets Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">
                @forelse ($ticketTypes as $index => $type)
                    @php
                        $remaining = $type->remaining_quota;
                        $isSoldOut = $remaining <= 0;
                        $isCivitas = $type->requires_student_id;
                        $isBundling = $type->people_per_ticket > 1;
                    @endphp

                    <div class="flex flex-col h-full rounded-3xl border-4 border-hf-purple-900 bg-white text-hf-purple-900 shadow-hard-lg overflow-hidden relative transition-all duration-200 hover:-translate-y-1.5 hover:shadow-[10px_10px_0_#2D0A4E]">
                        
                        <!-- Header Banner -->
                        <div class="p-4 {{ $isCivitas ? 'bg-hf-pink-500 text-white' : ($isBundling ? 'bg-hf-yellow-400 text-hf-purple-900' : 'bg-hf-purple-900 text-hf-yellow-400') }} border-b-3 border-hf-purple-900 flex justify-between items-center">
                            <span class="font-display text-sm tracking-wider uppercase font-bold">
                                {{ $isCivitas ? '🎓 KHUSUS CIVITAS' : ($isBundling ? '👥 PAKET BUNDLING 3 ORANG' : '🎫 TIKET UMUM') }}
                            </span>

                            @if ($isSoldOut)
                                <span class="px-2.5 py-0.5 bg-rose-600 text-white text-xs font-display rounded-lg border border-hf-purple-900">
                                    HABIS
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-white/20 text-xs font-sans font-bold rounded-lg">
                                    Sisa: {{ $remaining }}
                                </span>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="font-display text-2xl text-hf-purple-900 uppercase tracking-wide leading-tight mb-2">
                                    {{ $type->name }}
                                </h3>

                                <!-- Price -->
                                <div class="my-4">
                                    <div class="inline-flex items-baseline gap-1">
                                        <span class="text-sm font-sans font-bold text-gray-500">Rp</span>
                                        <span class="font-display text-4xl sm:text-5xl text-hf-purple-900 tracking-tight font-black">
                                            {{ number_format($type->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 font-sans mt-0.5">
                                        Berlaku untuk: <strong>{{ $type->people_per_ticket }} orang</strong>
                                    </p>
                                </div>

                                <!-- Description -->
                                <div class="p-3 bg-purple-50 rounded-2xl border-2 border-dashed border-purple-300 text-xs sm:text-sm text-gray-700 font-sans leading-relaxed my-4">
                                    {{ $type->description }}
                                </div>

                                <!-- Rules -->
                                <ul class="space-y-2 text-xs sm:text-sm font-sans text-gray-600 my-4">
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-500 font-bold">✓</span>
                                        <span>E-Tiket QR per orang langsung terbit</span>
                                    </li>
                                    @if ($isCivitas)
                                        <li class="flex items-center gap-2 text-hf-pink-500 font-bold">
                                            <span>⚠️</span>
                                            <span>Wajib upload foto identitas/KTM</span>
                                        </li>
                                    @endif
                                    @if ($isBundling)
                                        <li class="flex items-center gap-2 text-hf-purple-700 font-bold">
                                            <span>⭐</span>
                                            <span>Paket bundling lebih hemat</span>
                                        </li>
                                    @endif
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-500 font-bold">✓</span>
                                        <span>Batas pembayaran 24 jam</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Action -->
                            <div class="pt-4 border-t-2 border-gray-100">
                                @if ($isSoldOut)
                                    <button disabled class="w-full py-3 bg-gray-300 text-gray-500 border-3 border-gray-400 rounded-2xl font-display text-base uppercase tracking-wider cursor-not-allowed select-none">
                                        Tiket Sold Out 🚫
                                    </button>
                                @else
                                    @auth
                                        <a href="{{ route('orders.create', $type->id) }}" class="w-full inline-flex items-center justify-center py-3.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-display text-base uppercase tracking-wider shadow-hard btn-press hover:bg-hf-yellow-500">
                                            Pesan Tiket Ini 🔥
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center py-3.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-display text-base uppercase tracking-wider shadow-hard btn-press hover:bg-hf-yellow-500">
                                            Login untuk Memesan 🎟️
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-purple-200">
                        <p class="text-xl font-display">Belum ada kategori tiket yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Back to Home -->
            <div class="mt-12 text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 font-display text-sm uppercase text-hf-yellow-400 hover:text-white px-4 py-2 rounded-xl bg-hf-purple-900 border-2 border-hf-purple-900 shadow-hard-sm">
                    <span>← Kembali ke Beranda</span>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
