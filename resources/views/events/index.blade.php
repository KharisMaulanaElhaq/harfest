<x-app-layout>
    <x-slot:title>Daftar Seri Event Harfest - Harkat Negeri Fest</x-slot:title>

    <!-- Top Hero Header -->
    <section class="relative bg-hf-pattern pt-8 pb-12 sm:pt-12 sm:pb-16 px-4 sm:px-6 lg:px-8 border-b-4 border-hf-purple-900 text-center overflow-hidden">
        <div class="max-w-4xl mx-auto relative z-10">
            <x-section-title subtitle="SERI FESTIVAL MUSIK KAMPUS" plate="true">
                SEMUA EDISI HARFEST
            </x-section-title>
            <p class="text-sm sm:text-base md:text-lg text-pink-100 font-sans max-w-xl mx-auto mt-4 font-medium leading-relaxed">
                Jelajahi seluruh edisi Harfest Universitas Harkat Negeri, mulai dari edisi aktif yang sedang membuka penjualan hingga arsip panggung spektakuler masa lalu!
            </p>
        </div>
    </section>

    <!-- Section 1: Edisi Aktif & Segera Hadir -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-8">
            <span class="px-3 py-1 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase tracking-wider rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                EDISI AKTIF
            </span>
            <h2 class="font-sans font-black text-2xl sm:text-3xl text-white tracking-tight uppercase text-stroke-purple">
                Festival Mendatang & Sedang Dijual
            </h2>
        </div>

        @if ($activeEvents->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                @foreach ($activeEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>
        @else
            <div class="p-8 rounded-3xl border-4 border-hf-purple-900 bg-white/10 backdrop-blur-md text-center text-white font-sans">
                <p class="text-lg font-bold">Saat ini belum ada event baru yang membuka penjualan. Pantau terus info selanjutnya!</p>
            </div>
        @endif
    </section>

    <!-- Pattern Divider -->
    <x-pattern-divider variant="pink" />

    <!-- Section 2: Arsip Event Selesai -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-8">
            <span class="px-3 py-1 bg-hf-purple-900 text-hf-yellow-400 font-sans font-black text-xs uppercase tracking-wider rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                ARSIP SELESAI
            </span>
            <h2 class="font-sans font-black text-2xl sm:text-3xl text-white tracking-tight uppercase text-stroke-purple">
                Dokumentasi & Edisi Masa Lalu
            </h2>
        </div>

        @if ($finishedEvents->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-stretch">
                @foreach ($finishedEvents as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>
        @else
            <div class="p-8 rounded-3xl border-4 border-hf-purple-900 bg-white/10 backdrop-blur-md text-center text-white font-sans">
                <p class="text-lg font-bold">Belum ada arsip edisi selesai.</p>
            </div>
        @endif
    </section>
</x-app-layout>
