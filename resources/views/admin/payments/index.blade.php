<x-admin-layout>
    <x-slot:title>Verifikasi Pembayaran - Harfest Admin</x-slot:title>
    <x-slot:header>Verifikasi Pembayaran</x-slot:header>

    <div class="space-y-6">

        <!-- Top Stats / Filter Bar -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                    Daftar Pembayaran Masuk
                </h2>
                <p class="text-xs text-slate-500 font-sans mt-0.5">
                    Periksa bukti transfer dan cocokkan dengan mutasi rekening resmi Harfest.
                </p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-300 text-xs font-display">
                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900' }}">
                    <span>Menunggu</span>
                    @if ($pendingCount > 0)
                        <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ $statusFilter === 'pending' ? 'bg-hf-pink-500 text-white' : 'bg-slate-300 text-slate-800' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $statusFilter === 'approved' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900' }}">
                    Disetujui
                </a>

                <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $statusFilter === 'rejected' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900' }}">
                    Ditolak
                </a>

                <a href="{{ route('admin.payments.index', ['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $statusFilter === 'all' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900' }}">
                    Semua
                </a>
            </div>
        </div>

        <!-- Payments Table Card -->
        <div class="bg-white rounded-2xl border-3 border-hf-purple-900 shadow-hard overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left font-sans text-sm">
                    <thead class="bg-hf-purple-900 text-white font-display text-xs uppercase tracking-wider border-b-2 border-hf-purple-950">
                        <tr>
                            <th class="p-3.5 sm:p-4">Kode Booking</th>
                            <th class="p-3.5 sm:p-4">Pembeli</th>
                            <th class="p-3.5 sm:p-4">Tiket</th>
                            <th class="p-3.5 sm:p-4 text-right">Nominal</th>
                            <th class="p-3.5 sm:p-4">Info Pengirim</th>
                            <th class="p-3.5 sm:p-4 text-center">Status</th>
                            <th class="p-3.5 sm:p-4 text-center">Waktu</th>
                            <th class="p-3.5 sm:p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-purple-50/50 transition-colors">
                                <!-- Booking Code -->
                                <td class="p-3.5 sm:p-4">
                                    <span class="font-display text-sm sm:text-base text-hf-purple-900 tracking-wider block">
                                        {{ $payment->order->booking_code }}
                                    </span>
                                    @if ($payment->order->student_id)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-pink-100 text-hf-pink-600 border border-pink-200">
                                            🎓 CIVITAS
                                        </span>
                                    @endif
                                </td>

                                <!-- Buyer details -->
                                <td class="p-3.5 sm:p-4">
                                    <strong class="font-bold text-slate-900 block truncate max-w-[160px]">
                                        {{ $payment->order->user->name }}
                                    </strong>
                                    <span class="text-xs text-slate-500 block truncate max-w-[160px]">
                                        {{ $payment->order->user->phone }}
                                    </span>
                                </td>

                                <!-- Ticket -->
                                <td class="p-3.5 sm:p-4">
                                    <span class="font-bold text-slate-800 block text-xs">
                                        {{ $payment->order->ticketType->name }}
                                    </span>
                                    <span class="text-[11px] text-slate-500">
                                        {{ $payment->order->quantity }} paket ({{ $payment->order->quantity * $payment->order->ticketType->people_per_ticket }} orang)
                                    </span>
                                </td>

                                <!-- Nominal -->
                                <td class="p-3.5 sm:p-4 text-right font-display text-sm sm:text-base text-hf-purple-900 font-bold">
                                    Rp{{ number_format($payment->order->total_price, 0, ',', '.') }}
                                </td>

                                <!-- Bank / Sender -->
                                <td class="p-3.5 sm:p-4 text-xs">
                                    <span class="font-bold text-slate-700 block">
                                        {{ $payment->bank_name ?? 'Transfer Bank' }}
                                    </span>
                                    <span class="text-slate-500 truncate max-w-[120px] block">
                                        a.n. {{ $payment->sender_name ?? '-' }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="p-3.5 sm:p-4 text-center">
                                    <x-status-badge :status="$payment->status" />
                                </td>

                                <!-- Date -->
                                <td class="p-3.5 sm:p-4 text-center text-xs text-slate-500 whitespace-nowrap">
                                    {{ format_wib($payment->created_at) }}
                                </td>

                                <!-- Action -->
                                <td class="p-3.5 sm:p-4 text-right">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs btn-press">
                                        <span>Periksa</span>
                                        <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <span class="text-4xl block">✨</span>
                                        <h3 class="font-display text-lg text-hf-purple-900 uppercase">
                                            Tidak Ada Pembayaran
                                        </h3>
                                        <p class="text-xs text-slate-500 font-sans">
                                            @if ($statusFilter === 'pending')
                                                Semua bukti transfer yang masuk sudah selesai diverifikasi. Hebat!
                                            @else
                                                Belum ada data pembayaran untuk kategori status ini.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="p-4 border-t-2 border-slate-200 bg-slate-50">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
