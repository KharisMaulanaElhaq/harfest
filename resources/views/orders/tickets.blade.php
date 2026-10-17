<x-app-layout>
    <x-slot:title>E-Tiket {{ $order->booking_code }} - Harfest 2026</x-slot:title>

    <div class="py-10 md:py-16 bg-hf-pattern min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">

            <!-- Top Header & Actions (Hidden on Print) -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
                <div>
                    <a href="{{ route('orders.show', $order->booking_code) }}" 
                       class="inline-flex items-center gap-2 font-display text-xs sm:text-sm text-hf-yellow-400 hover:text-white bg-hf-purple-900/90 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                        <span>← Kembali ke Detail Pesanan</span>
                    </a>
                    <h1 class="font-display text-2xl sm:text-4xl text-hf-yellow-400 uppercase tracking-wide mt-3">
                        E-Tiket Resmi Harfest 2026
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-200 font-sans">
                        Kode Pesanan: <strong class="text-white">{{ $order->booking_code }}</strong> • Total: <strong class="text-hf-yellow-400">{{ $tickets->count() }} E-Tiket</strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="window.print()" 
                            class="px-5 py-3 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center gap-2 cursor-pointer">
                        <span>🖨️</span>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                </div>
            </div>

            <!-- Notice Banner (Hidden on Print) -->
            <div class="mb-6 p-4 bg-purple-900/90 text-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex items-start gap-3 no-print">
                <span class="text-2xl">💡</span>
                <div class="text-xs font-sans text-purple-200">
                    <strong class="text-white block font-display text-sm uppercase mb-0.5">Petunjuk Masuk Pintu Acara (Gate Check-in):</strong>
                    Tunjukkan kartu e-tiket di bawah ini langsung dari smartphone Anda atau hasil cetakan kepada panitia tiket di pintu masuk untuk discan. Tiap e-tiket memiliki QR unik dan hanya berlaku untuk 1 kali akses masuk.
                </div>
            </div>

            <!-- Tickets List (1 Card per Ticket) -->
            <div class="space-y-8">
                @foreach ($tickets as $index => $ticket)
                    <div class="ticket-card relative bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg overflow-hidden flex flex-col md:flex-row transition-all hover:-translate-y-1">
                        
                        <!-- LEFT SECTION: Main Concert Stub (~70%) -->
                        <div class="flex-1 p-6 sm:p-7 flex flex-col justify-between relative bg-gradient-to-br from-white via-white to-purple-50/60">
                            
                            <!-- Header Strip -->
                            <div>
                                <div class="flex items-center justify-between gap-2 border-b-3 border-hf-purple-900/20 pb-3 mb-4">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ asset('images/logo/harfest-logo.svg') }}" alt="Harfest" class="h-7 w-auto">
                                        <div>
                                            <span class="font-display text-lg text-hf-purple-900 tracking-wider block leading-none">HARFEST 2026</span>
                                            <span class="font-sans text-[10px] text-hf-pink-500 font-bold uppercase tracking-wider">Universitas Harkat Negeri</span>
                                        </div>
                                    </div>

                                    <div class="px-3 py-1 bg-hf-yellow-400 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs font-bold">
                                        {{ $order->ticketType->name }}
                                    </div>
                                </div>

                                <!-- Ticket Holder Name & Pass Number -->
                                <div class="my-2">
                                    <span class="text-[11px] font-sans uppercase font-bold text-gray-500 tracking-wider block">
                                        Nama Pemegang Tiket:
                                    </span>
                                    <h3 class="font-display text-2xl sm:text-3xl text-hf-purple-900 tracking-wide mt-0.5">
                                        {{ $ticket->holder_name }}
                                    </h3>
                                    <span class="text-xs font-sans text-gray-500 block mt-0.5">
                                        Tiket #{{ $index + 1 }} dari {{ $tickets->count() }} tiket pesanan (Ref: {{ $order->booking_code }})
                                    </span>
                                </div>
                            </div>

                            <!-- Concert Event Details -->
                            <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-sans">
                                <div>
                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">Tanggal & Waktu:</span>
                                    <strong class="text-slate-900 block font-bold">
                                        {{ $event?->event_date?->translatedFormat('l, d F Y') ?? '15 November 2026' }}
                                    </strong>
                                    <span class="text-gray-600">Open Gate: 14:00 WIB | Show: 15:00 WIB</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-[10px] uppercase font-bold">Lokasi Acara:</span>
                                    <strong class="text-slate-900 block font-bold">
                                        {{ $event?->venue ?? 'Lapangan Utama Kampus' }}
                                    </strong>
                                    <span class="text-gray-600">Universitas Harkat Negeri</span>
                                </div>
                            </div>

                            <!-- Ticket Rules Small Footer -->
                            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-sans">
                                <span>1 tiket berlaku untuk 1 kali masuk</span>
                                <span>Harfest Official Ticketing System</span>
                            </div>
                        </div>

                        <!-- PERFORATION / STUB CONNECTOR (Desktop vertical, mobile horizontal) -->
                        <div class="relative flex md:flex-col items-center justify-between bg-purple-100 md:w-0 border-t-4 md:border-t-0 md:border-l-4 border-dashed border-hf-purple-900/60 shrink-0">
                            <!-- Desktop top notch cutout -->
                            <div class="hidden md:block absolute -top-4 -left-3.5 w-7 h-7 rounded-full bg-hf-purple-700 border-b-4 border-hf-purple-900"></div>
                            <!-- Desktop bottom notch cutout -->
                            <div class="hidden md:block absolute -bottom-4 -left-3.5 w-7 h-7 rounded-full bg-hf-purple-700 border-t-4 border-hf-purple-900"></div>
                            
                            <!-- Mobile left notch cutout -->
                            <div class="md:hidden absolute -left-4 -top-3.5 w-7 h-7 rounded-full bg-hf-purple-700 border-r-4 border-hf-purple-900"></div>
                            <!-- Mobile right notch cutout -->
                            <div class="md:hidden absolute -right-4 -top-3.5 w-7 h-7 rounded-full bg-hf-purple-700 border-l-4 border-hf-purple-900"></div>
                        </div>

                        <!-- RIGHT SECTION: QR Stub (~30%) -->
                        <div class="p-6 md:w-64 bg-purple-50/80 flex flex-col items-center justify-center text-center shrink-0 border-t-4 md:border-t-0 border-hf-purple-900/20">
                            
                            <span class="font-display text-xs uppercase tracking-widest text-hf-purple-900 mb-2">
                                SCAN AT ENTRANCE
                            </span>

                            <!-- QR Code SVG from simple-qrcode -->
                            <div class="p-2.5 bg-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-xs inline-block">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(130)->color(75, 17, 128)->generate($ticket->ticket_code) !!}
                            </div>

                            <!-- Unique Ticket Code -->
                            <div class="mt-3">
                                <span class="font-mono font-black text-xs sm:text-sm text-hf-purple-900 tracking-wider block">
                                    {{ $ticket->ticket_code }}
                                </span>
                            </div>

                            <!-- Check-in Status Badge -->
                            <div class="mt-2.5">
                                @if ($ticket->is_used)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500 text-white font-display text-[11px] uppercase rounded-xl border border-hf-purple-900 shadow-xs">
                                        <span>⛔</span>
                                        <span>Sudah Digunakan</span>
                                    </span>
                                    <span class="text-[10px] text-gray-500 block mt-1 font-sans">
                                        Scan: {{ $ticket->used_at?->format('H:i') }} WIB
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500 text-white font-display text-[11px] uppercase rounded-xl border border-hf-purple-900 shadow-xs">
                                        <span>✅</span>
                                        <span>Siap Digunakan</span>
                                    </span>
                                @endif
                            </div>

                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Bottom Print Button (Hidden on Print) -->
            <div class="mt-10 text-center no-print">
                <button onclick="window.print()" 
                        class="px-8 py-4 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press inline-flex items-center gap-2 cursor-pointer">
                    <span>🖨️</span>
                    <span>Cetak Semua E-Tiket (PDF)</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Print Specific CSS -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print, header, nav, footer {
                display: none !important;
            }
            .ticket-card {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin-bottom: 24px !important;
                box-shadow: none !important;
                border: 2px solid #2D0A4E !important;
            }
        }
    </style>
</x-app-layout>
