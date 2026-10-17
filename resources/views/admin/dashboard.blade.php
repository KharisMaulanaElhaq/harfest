<x-admin-layout>
    <x-slot:title>Dashboard - Harfest Admin</x-slot:title>
    <x-slot:header>Ringkasan Eksekutif & Statistik</x-slot:header>

    <div class="space-y-8">

        <!-- 1. STATS METRICS CARDS (HARFEST THEMED) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            
            <!-- A. TOTAL PENDAPATAN -->
            <div class="p-5 sm:p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard flex flex-col justify-between transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-display uppercase tracking-wider text-slate-500">Total Pendapatan</span>
                    <span class="w-10 h-10 rounded-2xl bg-hf-yellow-400 text-hf-purple-900 flex items-center justify-center border-2 border-hf-purple-900 shadow-hard-xs text-xl">
                        💰
                    </span>
                </div>
                <div class="mt-4">
                    <span class="font-display text-2xl sm:text-3xl text-hf-purple-900 font-black block tracking-tight">
                        Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-emerald-600 font-sans font-bold flex items-center gap-1 mt-1">
                        <span>✓</span>
                        <span>Khusus dari {{ $paidOrdersCount }} pesanan lunas</span>
                    </span>
                </div>
            </div>

            <!-- B. TIKET TERJUAL -->
            <div class="p-5 sm:p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard flex flex-col justify-between transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-display uppercase tracking-wider text-slate-500">Tiket Terjual (Paid)</span>
                    <span class="w-10 h-10 rounded-2xl bg-hf-pink-500 text-white flex items-center justify-center border-2 border-hf-purple-900 shadow-hard-xs text-xl">
                        🎟️
                    </span>
                </div>
                <div class="mt-4">
                    <span class="font-display text-2xl sm:text-3xl text-hf-purple-900 font-black block tracking-tight">
                        {{ number_format($totalTicketsSold, 0, ',', '.') }} <span class="text-lg font-sans font-bold text-slate-500">tiket</span>
                    </span>
                    <span class="text-[11px] text-purple-700 font-sans font-medium block mt-1">
                        Akumulasi tiket masuk semua orang
                    </span>
                </div>
            </div>

            <!-- C. MENUNGGU VERIFIKASI -->
            <a href="{{ route('admin.payments.index') }}" 
               class="p-5 sm:p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard flex flex-col justify-between transition-all hover:-translate-y-1 group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-display uppercase tracking-wider text-slate-500">Menunggu Verifikasi</span>
                    <span class="w-10 h-10 rounded-2xl bg-hf-blue-400 text-hf-purple-900 flex items-center justify-center border-2 border-hf-purple-900 shadow-hard-xs text-xl {{ $waitingVerificationCount > 0 ? 'animate-bounce' : '' }}">
                        ⏳
                    </span>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-2">
                        <span class="font-display text-2xl sm:text-3xl font-black block tracking-tight {{ $waitingVerificationCount > 0 ? 'text-hf-pink-600' : 'text-hf-purple-900' }}">
                            {{ $waitingVerificationCount }}
                        </span>
                        <span class="text-xs font-sans text-slate-500 font-bold">pesanan</span>
                    </div>
                    <span class="text-[11px] text-hf-purple-800 font-sans font-bold group-hover:underline flex items-center gap-1 mt-1">
                        <span>Periksa sekarang</span>
                        <span>→</span>
                    </span>
                </div>
            </a>

            <!-- D. TOTAL TRANSAKSI LUNAS -->
            <div class="p-5 sm:p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard flex flex-col justify-between transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-display uppercase tracking-wider text-slate-500">Transaksi Lunas</span>
                    <span class="w-10 h-10 rounded-2xl bg-emerald-400 text-emerald-950 flex items-center justify-center border-2 border-hf-purple-900 shadow-hard-xs text-xl">
                        ✅
                    </span>
                </div>
                <div class="mt-4">
                    <span class="font-display text-2xl sm:text-3xl text-emerald-950 font-black block tracking-tight">
                        {{ $paidOrdersCount }}
                    </span>
                    <span class="text-[11px] text-emerald-700 font-sans font-medium block mt-1">
                        Pesanan dengan e-tiket aktif
                    </span>
                </div>
            </div>

        </div>

        <!-- 2. GRID: SISA KUOTA TIKET & RECENT ORDERS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT: SISA KUOTA KATEGORI TIKET (5 Cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📊</span>
                            <h3 class="font-display text-base text-hf-purple-900 uppercase">
                                Sisa Kuota Tiket
                            </h3>
                        </div>

                        <a href="{{ route('admin.ticket-types.index') }}" 
                           class="text-xs font-display text-hf-pink-600 hover:text-hf-pink-500 flex items-center gap-1">
                            <span>Kelola Tiket</span>
                            <span>→</span>
                        </a>
                    </div>

                    <div class="space-y-4">
                        @foreach ($ticketTypes as $type)
                            @php
                                $remaining = $type->remaining_quota;
                                $quota = max(1, $type->quota);
                                $used = max(0, $quota - $remaining);
                                $percentage = min(100, round(($used / $quota) * 100));
                            @endphp
                            <div class="p-3.5 bg-slate-50 rounded-2xl border-2 border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <strong class="font-display text-sm text-hf-purple-900 uppercase block truncate max-w-[180px]">
                                        {{ $type->name }}
                                    </strong>
                                    <span class="font-sans text-xs font-bold {{ $remaining <= 0 ? 'text-rose-600' : 'text-slate-700' }}">
                                        Sisa: <strong class="font-display text-sm">{{ $remaining }}</strong> / {{ $type->quota }}
                                    </span>
                                </div>

                                <!-- Progress Bar -->
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden border border-slate-300">
                                    <div class="h-3 rounded-full transition-all duration-500 {{ $percentage >= 90 ? 'bg-rose-500' : ($percentage >= 50 ? 'bg-hf-yellow-400' : 'bg-emerald-500') }}" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-500 font-sans">
                                    <span>Rp{{ number_format($type->price, 0, ',', '.') }} ({{ $type->people_per_ticket }} orang)</span>
                                    <span>Terpakai: {{ $percentage }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Shortcuts -->
                <div class="bg-gradient-to-r from-hf-purple-900 to-hf-purple-800 text-white p-5 rounded-3xl border-3 border-hf-purple-950 shadow-hard space-y-3">
                    <span class="font-display text-sm text-hf-yellow-400 uppercase tracking-wider block">Akses Cepat Gate:</span>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.checkin.index') }}" 
                           class="flex-1 py-3 px-4 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-950 shadow-hard-xs text-center">
                            📷 Buka Scanner Gate
                        </a>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex-1 py-3 px-4 bg-hf-pink-500 hover:bg-hf-pink-600 text-white font-display text-xs uppercase rounded-xl border-2 border-hf-purple-950 shadow-hard-xs text-center">
                            📦 Ekspor CSV Pesanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT: DAFTAR 5 PESANAN TERBARU (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📦</span>
                            <h3 class="font-display text-base text-hf-purple-900 uppercase">
                                Pesanan Terbaru
                            </h3>
                        </div>

                        <a href="{{ route('admin.orders.index') }}" 
                           class="text-xs font-display text-hf-purple-900 hover:underline flex items-center gap-1">
                            <span>Lihat Semua Pesanan</span>
                            <span>→</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-sans text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-display text-[11px] uppercase tracking-wider">
                                <tr>
                                    <th class="p-3">Kode Booking</th>
                                    <th class="p-3">Pembeli</th>
                                    <th class="p-3">Tiket</th>
                                    <th class="p-3 text-right">Total</th>
                                    <th class="p-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentOrders as $ro)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="p-3 font-mono font-bold text-hf-purple-900">
                                            {{ $ro->booking_code }}
                                        </td>
                                        <td class="p-3">
                                            <strong class="font-bold text-slate-800 block truncate max-w-[140px]">{{ $ro->user->name }}</strong>
                                            <span class="text-[10px] text-slate-400 block">{{ $ro->created_at->translatedFormat('d M H:i') }}</span>
                                        </td>
                                        <td class="p-3">
                                            <span class="block truncate max-w-[130px] font-medium text-slate-700">{{ $ro->ticketType->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $ro->quantity }} paket</span>
                                        </td>
                                        <td class="p-3 text-right font-display font-bold text-hf-purple-900 whitespace-nowrap">
                                            Rp{{ number_format($ro->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <x-status-badge :status="$ro->status" class="!px-2 !py-0.5 !text-[10px]" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                            Belum ada pesanan yang masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-admin-layout>
