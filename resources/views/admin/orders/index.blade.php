<x-admin-layout>
    <x-slot:title>Data Pesanan - Harfest Admin</x-slot:title>
    <x-slot:header>Manajemen Pesanan Tiket</x-slot:header>

    <div class="space-y-6">

        <!-- Top Search, Filter, and Export Bar -->
        <div class="bg-white p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                        Daftar Semua Pesanan
                    </h2>
                    <p class="text-xs text-slate-500 font-sans mt-0.5">
                        Filter, cari, dan pantau status transaksi pemesanan konser Harfest 2026.
                    </p>
                </div>

                <!-- Export CSV Button (FR-21) -->
                <div>
                    <a href="{{ route('admin.orders.export', request()->query()) }}" 
                       class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-display text-xs uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press cursor-pointer">
                        <span>📥</span>
                        <span>Ekspor CSV (Excel)</span>
                    </a>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 pt-2">
                <!-- Search input -->
                <div class="sm:col-span-8">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}" 
                               placeholder="Cari kode booking, nama pemesan, email, HP, atau NIM..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-slate-300 font-sans text-xs focus:outline-none focus:border-hf-purple-900">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-3">
                    <select name="status" 
                            class="w-full px-3 py-2.5 rounded-xl border-2 border-slate-300 font-sans text-xs focus:outline-none focus:border-hf-purple-900">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending_payment" {{ $statusFilter === 'pending_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
                        <option value="waiting_verification" {{ $statusFilter === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="expired" {{ $statusFilter === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                        <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="sm:col-span-1">
                    <button type="submit" 
                            class="w-full py-2.5 px-3 bg-hf-yellow-400 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 hover:bg-hf-yellow-500">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left font-sans text-xs">
                    <thead class="bg-hf-purple-900 text-white font-display uppercase tracking-wider border-b-2 border-hf-purple-950">
                        <tr>
                            <th class="p-3.5">Kode Booking</th>
                            <th class="p-3.5">Waktu</th>
                            <th class="p-3.5">Pembeli</th>
                            <th class="p-3.5">Kategori Tiket</th>
                            <th class="p-3.5 text-right">Total Bayar</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-purple-50/40 transition-colors">
                                <td class="p-3.5">
                                    <span class="font-mono font-bold text-sm text-hf-purple-900 block">
                                        {{ $order->booking_code }}
                                    </span>
                                    @if ($order->student_id)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-pink-100 text-pink-700 font-bold rounded text-[10px]">
                                            NIM: {{ $order->student_id }}
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3.5 text-slate-500 whitespace-nowrap">
                                    <div>{{ $order->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $order->created_at->format('H:i') }} WIB</div>
                                </td>

                                <td class="p-3.5">
                                    <strong class="font-bold text-slate-800 block">{{ $order->user->name }}</strong>
                                    <span class="text-slate-500 block text-[11px]">{{ $order->user->email }}</span>
                                    <span class="text-slate-400 block text-[10px]">{{ $order->user->phone }}</span>
                                </td>

                                <td class="p-3.5">
                                    <span class="font-bold text-slate-800 block">{{ $order->ticketType->name }}</span>
                                    <span class="text-slate-500 text-[11px]">
                                        {{ $order->quantity }} paket ({{ $order->quantity * $order->ticketType->people_per_ticket }} orang)
                                    </span>
                                </td>

                                <td class="p-3.5 text-right font-display text-sm font-bold text-hf-purple-900 whitespace-nowrap">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </td>

                                <td class="p-3.5 text-center whitespace-nowrap">
                                    <x-status-badge :status="$order->status" class="!px-2 !py-0.5 !text-[10px]" />
                                </td>

                                <td class="p-3.5 text-right whitespace-nowrap">
                                    @if ($order->latestPayment)
                                        <a href="{{ route('admin.payments.show', $order->latestPayment->id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-[11px] uppercase rounded-lg border border-hf-purple-900">
                                            <span>Bukti</span>
                                            <span>→</span>
                                        </a>
                                    @elseif ($order->status === \App\Enums\OrderStatus::Paid)
                                        <a href="{{ route('orders.tickets', $order->booking_code) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-hf-purple-900 text-hf-yellow-400 font-display text-[11px] uppercase rounded-lg">
                                            <span>Tiket</span>
                                            <span>↗</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-10 text-center text-slate-400 italic font-sans">
                                    Tidak ada data pesanan yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t-2 border-slate-200 bg-slate-50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
