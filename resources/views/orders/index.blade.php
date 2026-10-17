<x-app-layout>
    <x-slot:title>Riwayat Pesanan Tiket - Harfest 2026</x-slot:title>

    <div class="py-12 md:py-20 bg-hf-pattern min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="font-display text-3xl sm:text-4xl text-hf-yellow-400 text-stroke-purple-thick uppercase tracking-wide drop-shadow-[3px_3px_0_#2D0A4E]">
                        Riwayat Pesanan
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-200 font-sans mt-1">
                        Daftar seluruh transaksi pemesanan tiket Harfest milik Anda
                    </p>
                </div>

                <div>
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-display text-sm uppercase tracking-wide shadow-hard-sm btn-press hover:bg-hf-yellow-500">
                        <span>+ Pesan Tiket Baru</span>
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            @if ($orders->isEmpty())
                <!-- Empty State -->
                <div class="bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg p-10 text-center flex flex-col items-center">
                    <x-mascot type="blob" size="lg" class="mb-4" />
                    <h3 class="font-display text-2xl text-hf-purple-900 uppercase mb-2">
                        Belum Ada Pesanan Tiket
                    </h3>
                    <p class="text-sm text-gray-600 font-sans max-w-md mb-6 leading-relaxed">
                        Anda belum memesan tiket konser Harfest. Yuk pilih tiket dan amankan tempatmu sebelum kuota habis!
                    </p>
                    <a href="{{ route('tickets.index') }}" class="px-6 py-3 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-display text-base uppercase tracking-wider shadow-hard btn-press hover:bg-hf-yellow-500">
                        Lihat Kategori Tiket 🔥
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard p-5 sm:p-6 transition-all duration-200 hover:shadow-hard-lg hover:-translate-y-0.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-gray-100">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-display text-lg sm:text-xl text-hf-purple-900 tracking-wider">
                                            {{ $order->booking_code }}
                                        </span>
                                        <x-status-badge :status="$order->status" />
                                    </div>
                                    <span class="text-xs text-gray-500 font-sans block mt-1">
                                        Dipesan pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
                                    </span>
                                </div>

                                <div class="text-left sm:text-right">
                                    <span class="text-xs text-gray-500 font-sans block">Total Tagihan:</span>
                                    <span class="font-display text-2xl sm:text-3xl text-hf-purple-900 font-black">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Details bar -->
                            <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-display text-lg text-hf-purple-900 uppercase">
                                        {{ $order->ticketType->name }}
                                    </h4>
                                    <p class="text-xs text-gray-600 font-sans">
                                        Jumlah: <strong>{{ $order->quantity }} paket</strong> 
                                        ({{ $order->quantity * $order->ticketType->people_per_ticket }} orang)
                                        @if ($order->student_id)
                                            • NIM: <strong>{{ $order->student_id }}</strong>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <a href="{{ route('orders.show', $order->booking_code) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-hf-purple-700 text-hf-yellow-400 border-2 border-hf-purple-900 rounded-2xl font-display text-sm uppercase tracking-wide shadow-hard-sm btn-press hover:bg-hf-purple-500">
                                        <span>Lihat Detail & Bayar</span>
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
