<x-app-layout>
    <x-slot:title>E-Tiket {{ $order->booking_code }} - {{ $event?->name ?? 'Harfest' }}</x-slot:title>

    <div class="py-10 md:py-16 bg-hf-pattern min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">

            <!-- Top Header & Actions (Hidden on Print) -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
                <div>
                    <a href="{{ route('orders.show', $order->booking_code) }}" 
                       class="inline-flex items-center gap-2 font-sans font-bold text-xs sm:text-sm text-hf-yellow-400 hover:text-white bg-hf-purple-900/90 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                        <span>← Kembali ke Detail Pesanan</span>
                    </a>
                    <h1 class="font-sans font-black text-2xl sm:text-4xl text-hf-yellow-400 uppercase tracking-wide mt-3 drop-shadow-[2px_2px_0_#4D0570]">
                        E-Tiket Resmi {{ $event?->name ?? 'Harfest 2026' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-200 font-sans mt-1">
                        Kode Pesanan: <strong class="text-white">{{ $order->booking_code }}</strong> • Total: <strong class="text-hf-yellow-400">{{ $tickets->count() }} E-Tiket</strong>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('orders.tickets.download', $order->booking_code) }}" 
                       class="px-5 py-3 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-sans font-black text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center gap-2 cursor-pointer">
                        <span>📥</span>
                        <span>Unduh Semua PDF</span>
                    </a>
                    <button onclick="window.print()" 
                            class="px-4 py-3 bg-white hover:bg-purple-100 text-hf-purple-900 font-sans font-bold text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center gap-2 cursor-pointer">
                        <span>🖨️</span>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                </div>
            </div>

            <!-- Notice Banner (Hidden on Print) -->
            <div class="mb-6 p-4 bg-purple-900/90 text-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex items-start gap-3 no-print">
                <span class="text-2xl">💡</span>
                <div class="text-xs font-sans text-purple-200">
                    <strong class="text-white block font-sans font-black text-sm uppercase mb-0.5">Petunjuk Masuk Pintu Acara (Gate Check-in):</strong>
                    Tunjukkan kartu e-tiket di bawah ini langsung dari smartphone Anda atau hasil cetakan kepada panitia tiket di pintu masuk untuk discan. Tiap e-tiket memiliki QR unik dan hanya berlaku untuk 1 kali akses masuk.
                </div>
            </div>

            <!-- Tickets List (1 Card per Ticket) -->
            <div class="space-y-8">
                @foreach ($tickets as $index => $ticket)
                    <x-eticket 
                        :ticket="$ticket" 
                        :order="$order" 
                        :event="$event" 
                        :index="$index" 
                        :total="$tickets->count()" 
                    />
                @endforeach
            </div>

            <!-- Bottom Action Button (Hidden on Print) -->
            <div class="mt-10 text-center no-print flex flex-wrap justify-center items-center gap-4">
                <a href="{{ route('orders.tickets.download', $order->booking_code) }}" 
                   class="px-8 py-4 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-sans font-black text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press inline-flex items-center gap-2 cursor-pointer">
                    <span>📥</span>
                    <span>Unduh Semua E-Tiket (PDF)</span>
                </a>
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
                border: 2px solid #4D0570 !important;
            }
        }
    </style>
</x-app-layout>
