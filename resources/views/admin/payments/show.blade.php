<x-admin-layout>
    <x-slot:title>Verifikasi {{ $payment->order->booking_code }} - Harfest Admin</x-slot:title>
    <x-slot:header>Detail Pembayaran & Verifikasi</x-slot:header>

    <div class="space-y-6">

        <!-- Top Breadcrumbs / Back Link -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.payments.index') }}" 
               class="inline-flex items-center gap-2 font-display text-xs sm:text-sm text-hf-purple-900 bg-white hover:bg-slate-50 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                <span>← Kembali ke Daftar Pembayaran</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-sans hidden sm:inline">Status Bukti:</span>
                <x-status-badge :status="$payment->status" />
            </div>
        </div>

        <!-- Order Header Banner -->
        <div class="bg-hf-purple-900 text-white p-6 rounded-3xl border-4 border-hf-purple-950 shadow-hard flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs text-purple-300 font-sans block uppercase tracking-wider">KODE BOOKING PESANAN</span>
                <div class="flex items-center gap-3 mt-1">
                    <h2 class="font-display text-2xl sm:text-3xl text-hf-yellow-400 tracking-wider">
                        {{ $payment->order->booking_code }}
                    </h2>
                    <x-status-badge :status="$payment->order->status" />
                </div>
            </div>

            <div class="text-left sm:text-right font-sans">
                <span class="text-xs text-purple-300 block">Dipesan pada:</span>
                <span class="font-bold text-sm text-white block">
                    {{ $payment->order->created_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
                <span class="text-xs text-purple-300">
                    Batas Bayar: {{ $payment->order->expires_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            </div>
        </div>

        <!-- 2 Columns Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN: Bukti Transfer & Data Civitas (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Bukti Transfer Card -->
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">📸</span>
                            <h3 class="font-display text-lg text-hf-purple-900 uppercase">
                                Bukti Transfer Pembayaran
                            </h3>
                        </div>

                        <a href="{{ route('files.payment', $payment->id) }}" 
                           target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-hf-purple-900 text-hf-yellow-400 font-display text-xs rounded-xl hover:bg-hf-purple-800 transition-colors">
                            <span>Buka Asli</span>
                            <span>↗</span>
                        </a>
                    </div>

                    <!-- Bukti Preview Container -->
                    @php
                        $isPdf = str_ends_with(strtolower($payment->proof_path ?? ''), '.pdf');
                    @endphp

                    <div class="bg-slate-100 rounded-2xl border-2 border-slate-300 p-2 sm:p-4 text-center overflow-hidden">
                        @if ($isPdf)
                            <div class="py-10 space-y-3">
                                <span class="text-5xl block">📄</span>
                                <span class="font-display text-sm text-hf-purple-900 block">
                                    Dokumen Bukti Transfer (PDF)
                                </span>
                                <a href="{{ route('files.payment', $payment->id) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white font-display text-xs rounded-xl shadow-hard-xs hover:bg-rose-700">
                                    <span>Lihat File PDF di Tab Baru</span>
                                    <span>↗</span>
                                </a>
                            </div>
                        @else
                            <div class="relative group">
                                <img src="{{ route('files.payment', $payment->id) }}" 
                                     alt="Bukti Transfer {{ $payment->order->booking_code }}" 
                                     class="max-h-[460px] mx-auto rounded-xl border-2 border-slate-300 shadow-sm object-contain bg-white">
                                <div class="mt-2 text-center">
                                    <span class="text-xs text-slate-500 font-sans">
                                        Klik "Buka Asli ↗" di sudut kanan atas untuk melihat resolusi penuh.
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Detail Upload Metadata -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-sans p-3 bg-purple-50 rounded-2xl border border-purple-200">
                        <div>
                            <span class="text-slate-500 block">Bank / Dompet Digital Pengirim:</span>
                            <strong class="font-bold text-hf-purple-900 text-sm">{{ $payment->bank_name ?: '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Nama Pemilik Rekening:</span>
                            <strong class="font-bold text-hf-purple-900 text-sm">{{ $payment->sender_name ?: '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Waktu Pengunggahan:</span>
                            <span class="text-slate-800 font-medium">{{ $payment->created_at->translatedFormat('d F Y, H:i:s') }} WIB</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Rekening Tujuan Panitia:</span>
                            <span class="text-slate-800 font-medium">{{ config('harfest.bank.name') }} - {{ config('harfest.bank.account_number') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Data Civitas (Jika Ada) -->
                @if ($payment->order->student_id)
                    <div class="bg-white rounded-3xl border-3 border-hf-pink-500 shadow-hard p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b-2 border-pink-100 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-2xl">🎓</span>
                                <h3 class="font-display text-lg text-hf-pink-600 uppercase">
                                    Verifikasi Civitas Akademika
                                </h3>
                            </div>

                            @if ($payment->order->student_card_path)
                                <a href="{{ route('files.student-card', $payment->order->booking_code) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1 bg-hf-pink-500 text-white font-display text-xs rounded-xl hover:bg-hf-pink-600 transition-colors">
                                    <span>Buka Foto KTM</span>
                                    <span>↗</span>
                                </a>
                            @endif
                        </div>

                        <div class="p-3 bg-pink-50 rounded-2xl border border-pink-200 flex items-center justify-between">
                            <span class="text-xs text-pink-900 font-sans">NIM / NIP Mahasiswa / Dosen:</span>
                            <span class="font-display text-base text-pink-950 font-black tracking-wider">
                                {{ $payment->order->student_id }}
                            </span>
                        </div>

                        @if ($payment->order->student_card_path)
                            <div class="bg-slate-50 rounded-2xl border-2 border-pink-200 p-2 text-center">
                                <img src="{{ route('files.student-card', $payment->order->booking_code) }}" 
                                     alt="Foto KTM {{ $payment->order->student_id }}" 
                                     class="max-h-60 mx-auto rounded-xl border border-pink-300 shadow-xs object-contain">
                                <span class="text-[11px] text-pink-700 block mt-1.5 font-sans">
                                    Cocokkan nama pemesan ({{ $payment->order->user->name }}) dengan data pada KTM di atas.
                                </span>
                            </div>
                        @else
                            <p class="text-xs text-slate-500 italic">Tidak ada lampiran foto KTM.</p>
                        @endif
                    </div>
                @endif

            </div>

            <!-- RIGHT COLUMN: Order Summary & Action Forms (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Ringkasan Tagihan Card -->
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-4">
                    <h3 class="font-display text-lg text-hf-purple-900 uppercase border-b-2 border-slate-100 pb-3 flex items-center gap-2">
                        <span>📋</span>
                        <span>Rincian Tagihan</span>
                    </h3>

                    <!-- Buyer Info -->
                    <div class="space-y-1.5 text-xs font-sans pb-3 border-b border-slate-100">
                        <span class="text-slate-500 block uppercase font-bold text-[10px]">Data Pembeli:</span>
                        <div class="font-bold text-slate-900 text-sm">{{ $payment->order->user->name }}</div>
                        <div class="text-slate-600">{{ $payment->order->user->email }}</div>
                        <div class="text-slate-600">{{ $payment->order->user->phone }}</div>
                    </div>

                    <!-- Ticket Type & Qty -->
                    <div class="space-y-2 text-xs font-sans pb-3 border-b border-slate-100">
                        <span class="text-slate-500 block uppercase font-bold text-[10px]">Tiket Dipesan:</span>
                        <div class="flex justify-between items-start">
                            <div>
                                <strong class="font-bold text-hf-purple-900 text-sm block">{{ $payment->order->ticketType->name }}</strong>
                                <span class="text-slate-500 text-[11px]">Rp{{ number_format($payment->order->ticketType->price, 0, ',', '.') }} × {{ $payment->order->quantity }} paket</span>
                            </div>
                            <span class="px-2 py-1 bg-purple-100 text-hf-purple-900 rounded-lg font-bold">
                                {{ $payment->order->quantity * $payment->order->ticketType->people_per_ticket }} orang
                            </span>
                        </div>
                    </div>

                    <!-- Total Price Highlight -->
                    <div class="p-4 bg-hf-yellow-100 rounded-2xl border-2 border-hf-purple-900 text-center">
                        <span class="text-xs font-sans text-hf-purple-900 block font-bold uppercase">
                            Total Yang Harus Diterima:
                        </span>
                        <span class="font-display text-3xl sm:text-4xl text-hf-purple-900 font-black block mt-0.5">
                            Rp{{ number_format($payment->order->total_price, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-purple-950 font-sans block mt-1">
                            Harap pastikan mutasi masuk tepat Rp{{ number_format($payment->order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- AKSI VERIFIKASI CARD -->
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-5">
                    <h3 class="font-display text-lg text-hf-purple-900 uppercase border-b-2 border-slate-100 pb-3 flex items-center gap-2">
                        <span>⚖️</span>
                        <span>Keputusan Panitia</span>
                    </h3>

                    @if ($payment->status === \App\Enums\PaymentStatus::Pending)
                        
                        <!-- 1. Form Approve -->
                        <form action="{{ route('admin.payments.approve', $payment->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI pembayaran ini? Pesanan akan diubah menjadi PAID dan E-Tiket QR akan langsung diterbitkan.');">
                            @csrf
                            <button type="submit" 
                                    class="w-full py-3.5 px-6 bg-emerald-500 hover:bg-emerald-600 text-white font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center justify-center gap-2 cursor-pointer">
                                <span>✓</span>
                                <span>Terima Pembayaran (Lunas)</span>
                            </button>
                        </form>

                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t-2 border-slate-200"></div>
                            <span class="shrink mx-3 text-xs font-display text-slate-400 uppercase">Atau</span>
                            <div class="flex-grow border-t-2 border-slate-200"></div>
                        </div>

                        <!-- 2. Form Reject -->
                        <div x-data="{ showRejectModal: false }">
                            <button type="button" 
                                    @click="showRejectModal = !showRejectModal"
                                    class="w-full py-3 px-6 bg-rose-50 hover:bg-rose-100 text-rose-700 font-display text-sm uppercase rounded-2xl border-2 border-rose-400 shadow-hard-xs btn-press flex items-center justify-center gap-2">
                                <span>✕</span>
                                <span>Tolak Pembayaran Ini...</span>
                            </button>

                            <!-- Reject Form Box -->
                            <div x-show="showRejectModal" 
                                 x-transition 
                                 class="mt-4 p-4 bg-rose-50 rounded-2xl border-2 border-rose-400 space-y-3">
                                
                                <form action="{{ route('admin.payments.reject', $payment->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK bukti transfer ini? Pembeli akan diminta mengunggah ulang bukti baru.');">
                                    @csrf

                                    <label for="reject_reason" class="block font-display text-xs uppercase tracking-wider text-rose-900 mb-1">
                                        Alasan Penolakan <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea id="reject_reason" 
                                              name="reject_reason" 
                                              rows="3" 
                                              required 
                                              placeholder="Tulis alasan jelas untuk pembeli, misal: Nominal transfer kurang Rp 10.000, mutasi belum masuk di rekening BCA, atau foto bukti blur." 
                                              class="w-full p-2.5 text-xs font-sans rounded-xl border-2 border-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white">{{ old('reject_reason') }}</textarea>

                                    @error('reject_reason')
                                        <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                    @enderror

                                    <div class="pt-2 flex gap-2">
                                        <button type="submit" 
                                                class="flex-1 py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                                            Konfirmasi Penolakan
                                        </button>
                                        <button type="button" 
                                                @click="showRejectModal = false"
                                                class="px-3 py-2.5 bg-slate-200 text-slate-700 font-display text-xs rounded-xl hover:bg-slate-300">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @elseif ($payment->status === \App\Enums\PaymentStatus::Approved)
                        <div class="p-4 bg-emerald-50 rounded-2xl border-2 border-emerald-400 space-y-2 text-xs font-sans text-emerald-950">
                            <div class="flex items-center gap-2 font-display text-sm text-emerald-800 uppercase">
                                <span>🎉</span>
                                <span>Pembayaran Disetujui</span>
                            </div>
                            <p>Diverifikasi oleh: <strong>{{ $payment->verifier?->name ?? 'Admin' }}</strong></p>
                            <p>Waktu Verifikasi: <strong>{{ $payment->verified_at?->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                            <p class="text-emerald-700 pt-1">
                                Tiket elektronik resmi sudah terbit dan dikirimkan ke akun pemesan.
                            </p>
                        </div>
                    @elseif ($payment->status === \App\Enums\PaymentStatus::Rejected)
                        <div class="p-4 bg-rose-50 rounded-2xl border-2 border-rose-400 space-y-2 text-xs font-sans text-rose-950">
                            <div class="flex items-center gap-2 font-display text-sm text-rose-800 uppercase">
                                <span>⚠️</span>
                                <span>Pembayaran Ditolak</span>
                            </div>
                            <p>Ditolak oleh: <strong>{{ $payment->verifier?->name ?? 'Admin' }}</strong></p>
                            <p>Waktu: <strong>{{ $payment->verified_at?->translatedFormat('d F Y, H:i') }} WIB</strong></p>
                            <div class="p-2.5 bg-white rounded-xl border border-rose-200 mt-2">
                                <span class="text-slate-500 block text-[10px] uppercase font-bold">Alasan Penolakan:</span>
                                <span class="font-medium text-rose-950">{{ $payment->reject_reason }}</span>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>
</x-admin-layout>
