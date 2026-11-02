<x-app-layout>
    <x-slot:title>Pesanan {{ $order->booking_code }} - Harfest 2026</x-slot:title>

    <div class="py-12 md:py-20 bg-hf-pattern min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            
            <!-- Breadcrumbs -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 font-display text-xs sm:text-sm text-hf-yellow-400 hover:text-white bg-hf-purple-900/90 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                    <span>← Kembali ke Riwayat Pesanan</span>
                </a>

                <div class="text-xs text-purple-200 font-sans hidden sm:block">
                    Status diperbarui: {{ $order->updated_at->diffForHumans() }}
                </div>
            </div>

            <!-- Main Order Detail Container -->
            <div class="bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg overflow-hidden">
                
                <!-- Header Banner -->
                <div class="p-6 bg-hf-purple-900 text-white border-b-4 border-hf-purple-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-purple-300 font-sans block uppercase tracking-wider">KODE BOOKING PESANAN</span>
                        <div class="flex items-center gap-3 mt-1">
                            <h1 class="font-display text-2xl sm:text-4xl text-hf-yellow-400 tracking-wider">
                                {{ $order->booking_code }}
                            </h1>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="text-xs text-purple-300 block font-sans">Tanggal Pemesanan:</span>
                        <span class="font-sans font-bold text-sm sm:text-base text-white">
                            {{ format_wib($order->created_at) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-8">

                    <!-- SECTION 1: PEMBAYARAN JIKA PENDING_PAYMENT -->
                    @if ($order->status === \App\Enums\OrderStatus::PendingPayment)
                        <div class="p-6 bg-purple-50 rounded-3xl border-3 border-hf-purple-900 shadow-hard space-y-6">
                            
                            <!-- Countdown Timer with Alpine.js -->
                            <div x-data="{
                                    expiry: new Date('{{ $order->expires_at->toIso8601String() }}').getTime(),
                                    now: new Date().getTime(),
                                    hours: 0,
                                    minutes: 0,
                                    seconds: 0,
                                    isExpired: false,
                                    init() {
                                        this.updateTimer();
                                        setInterval(() => {
                                            this.now = new Date().getTime();
                                            this.updateTimer();
                                        }, 1000);
                                    },
                                    updateTimer() {
                                        const diff = this.expiry - this.now;
                                        if (diff <= 0) {
                                            this.isExpired = true;
                                            this.hours = 0;
                                            this.minutes = 0;
                                            this.seconds = 0;
                                        } else {
                                            this.hours = Math.floor(diff / (1000 * 60 * 60));
                                            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                        }
                                    }
                                }"
                                class="p-4 bg-hf-yellow-400 text-hf-purple-900 rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm text-center">
                                
                                <span class="text-xs font-display uppercase tracking-wider block font-bold">
                                    ⏱️ SISA WAKTU PEMBAYARAN
                                </span>

                                <template x-if="!isExpired">
                                    <div class="flex justify-center items-center gap-2 sm:gap-3 my-2 font-display text-2xl sm:text-4xl font-black">
                                        <div class="bg-hf-purple-900 text-hf-yellow-400 px-3 py-1.5 rounded-xl border-2 border-hf-purple-900">
                                            <span x-text="String(hours).padStart(2, '0')">00</span>
                                            <span class="text-[10px] block font-sans uppercase -mt-1 text-purple-200">Jam</span>
                                        </div>
                                        <span>:</span>
                                        <div class="bg-hf-purple-900 text-hf-yellow-400 px-3 py-1.5 rounded-xl border-2 border-hf-purple-900">
                                            <span x-text="String(minutes).padStart(2, '0')">00</span>
                                            <span class="text-[10px] block font-sans uppercase -mt-1 text-purple-200">Menit</span>
                                        </div>
                                        <span>:</span>
                                        <div class="bg-hf-purple-900 text-hf-yellow-400 px-3 py-1.5 rounded-xl border-2 border-hf-purple-900">
                                            <span x-text="String(seconds).padStart(2, '0')">00</span>
                                            <span class="text-[10px] block font-sans uppercase -mt-1 text-purple-200">Detik</span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="isExpired">
                                    <div class="py-2 text-rose-700 font-display text-xl uppercase">
                                        ⚠️ Batas waktu pembayaran telah habis
                                    </div>
                                </template>

                                <p class="text-xs text-purple-950 font-sans">
                                    Batas pembayaran: <strong>{{ format_wib($order->expires_at) }}</strong>
                                </p>
                            </div>

                            <!-- Total Transfer Nominal -->
                            <div class="text-center py-2" x-data="{ copied: false }">
                                <span class="text-xs text-gray-500 font-sans block uppercase font-bold">Jumlah Total Yang Harus Ditransfer:</span>
                                <div class="inline-flex items-center gap-3 mt-1">
                                    <span class="font-display text-3xl sm:text-5xl text-hf-purple-900 font-black">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </span>
                                    <button type="button" 
                                            @click="navigator.clipboard.writeText('{{ $order->total_price }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="px-3 py-1.5 bg-hf-purple-700 text-hf-yellow-400 rounded-xl border-2 border-hf-purple-900 text-xs font-display btn-press">
                                        <span x-show="!copied">Salin Nominal</span>
                                        <span x-show="copied" style="display: none;">Tersalin! ✓</span>
                                    </button>
                                </div>
                                <p class="text-xs text-rose-600 font-sans font-medium mt-1">
                                    *Mohon transfer tepat sesuai nominal di atas tanpa pembulatan.
                                </p>
                            </div>

                            <!-- Rekening Panitia & QRIS Card -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                <!-- Rekening Bank -->
                                <div class="p-5 bg-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col justify-between" x-data="{ copied: false }">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-display text-lg text-hf-purple-900 uppercase">
                                                Transfer Bank {{ config('harfest.bank.name') }}
                                            </span>
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-bold rounded-lg">
                                                Manual
                                            </span>
                                        </div>
                                        
                                        <p class="text-xs text-gray-500 font-sans">Nomor Rekening Resmi Panitia:</p>
                                        <div class="flex items-center justify-between mt-1 p-2 bg-purple-50 rounded-xl border border-purple-200">
                                            <span class="font-display text-xl text-hf-purple-900 tracking-wider">
                                                {{ config('harfest.bank.account_number') }}
                                            </span>
                                            <button type="button" 
                                                    @click="navigator.clipboard.writeText('{{ config('harfest.bank.account_number') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="px-2.5 py-1 bg-hf-yellow-400 text-hf-purple-900 text-xs font-display rounded-lg border border-hf-purple-900 hover:bg-hf-yellow-500">
                                                <span x-show="!copied">Salin</span>
                                                <span x-show="copied" style="display: none;">✓</span>
                                            </button>
                                        </div>

                                        <p class="text-xs text-gray-600 font-sans mt-2">
                                            Atas Nama: <strong>{{ config('harfest.bank.account_holder') }}</strong>
                                        </p>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500">
                                        Cantumkan berita transfer: <strong>{{ $order->booking_code }}</strong>
                                    </div>
                                </div>

                                <!-- QRIS info -->
                                <div class="p-5 bg-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-sm flex flex-col items-center text-center justify-between">
                                    <div>
                                        <span class="font-display text-lg text-hf-purple-900 uppercase block mb-1">
                                            QRIS Panitia Harfest
                                        </span>
                                        <p class="text-xs text-gray-500 font-sans mb-3">
                                            Scan via BCA, Mandiri, GoPay, OVO, Dana, ShopeePay
                                        </p>
                                        
                                        <!-- QRIS Placeholder Box -->
                                        <div class="w-36 h-36 bg-gray-100 rounded-2xl border-2 border-dashed border-hf-purple-900 p-2 flex flex-col items-center justify-center mx-auto shadow-sm">
                                            <svg class="w-20 h-20 text-hf-purple-900 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                            </svg>
                                            <span class="text-[10px] text-gray-500 font-sans mt-1">QRIS Panitia</span>
                                        </div>
                                    </div>

                                    <span class="text-[11px] text-gray-500 mt-3">
                                        Simpan tangkapan layar bukti bayar setelah transfer berhasil.
                                    </span>
                                </div>
                            </div>

                            <!-- Upload Proof Section Form (FR-08) -->
                            <div class="mt-6 p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="text-2xl">📤</span>
                                    <div>
                                        <h4 class="font-display text-xl text-hf-purple-900 uppercase">
                                            Unggah Bukti Transfer
                                        </h4>
                                        <p class="text-xs text-gray-600 font-sans">
                                            Format: JPG, JPEG, PNG, atau PDF (Maksimal 2 MB).
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('orders.payment', $order->booking_code) }}" 
                                      method="POST" 
                                      enctype="multipart/form-data" 
                                      class="space-y-4"
                                      x-data="{ 
                                          fileName: '', 
                                          isImage: false, 
                                          imagePreview: null,
                                          fileChosen(event) {
                                              const file = event.target.files[0];
                                              if (!file) return;
                                              this.fileName = file.name;
                                              this.isImage = file.type.startsWith('image/');
                                              if (this.isImage) {
                                                  const reader = new FileReader();
                                                  reader.onload = (e) => { this.imagePreview = e.target.result; };
                                                  reader.readAsDataURL(file);
                                              } else {
                                                  this.imagePreview = null;
                                              }
                                          }
                                      }">
                                    @csrf

                                    <!-- File Upload Dropzone -->
                                    <div>
                                        <label for="proof" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1.5">
                                            File Bukti Transfer <span class="text-hf-pink-500">*</span>
                                        </label>
                                        
                                        <div class="relative border-3 border-dashed border-hf-purple-900/60 hover:border-hf-purple-900 rounded-2xl p-4 sm:p-6 text-center bg-purple-50/50 hover:bg-purple-50 transition-colors cursor-pointer"
                                             @click="$refs.fileInput.click()">
                                            <input type="file" 
                                                   id="proof" 
                                                   name="proof" 
                                                   x-ref="fileInput" 
                                                   @change="fileChosen" 
                                                   accept=".jpg,.jpeg,.png,.pdf" 
                                                   class="hidden" 
                                                   required>

                                            <template x-if="!fileName">
                                                <div class="space-y-1">
                                                    <span class="text-3xl block">📎</span>
                                                    <span class="font-display text-sm text-hf-purple-900 block">
                                                        Klik di sini untuk memilih file
                                                    </span>
                                                    <span class="text-[11px] text-gray-500 font-sans block">
                                                        JPG, PNG, atau PDF hingga 2 MB
                                                    </span>
                                                </div>
                                            </template>

                                            <template x-if="fileName">
                                                <div class="space-y-2">
                                                    <template x-if="imagePreview">
                                                        <img :src="imagePreview" alt="Pratinjau Bukti" class="max-h-48 mx-auto rounded-xl border-2 border-hf-purple-900 shadow-sm object-contain">
                                                    </template>
                                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-hf-purple-900 text-hf-yellow-400 font-display text-xs rounded-xl border border-hf-purple-900">
                                                        <span>📄</span>
                                                        <span x-text="fileName" class="truncate max-w-xs"></span>
                                                        <span class="text-white hover:text-rose-300 ml-1 font-sans">✕ Ganti</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        @error('proof')
                                            <p class="text-xs text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Optional Bank & Sender details -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="bank_name" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                                Bank / Dompet Digital Pengirim
                                            </label>
                                            <input type="text" 
                                                   id="bank_name" 
                                                   name="bank_name" 
                                                   value="{{ old('bank_name') }}" 
                                                   placeholder="Misal: BCA, Mandiri, GoPay, QRIS" 
                                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 font-sans text-sm">
                                            @error('bank_name')
                                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="sender_name" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                                Nama Pemilik Rekening Pengirim
                                            </label>
                                            <input type="text" 
                                                   id="sender_name" 
                                                   name="sender_name" 
                                                   value="{{ old('sender_name') }}" 
                                                   placeholder="Nama sesuai mutasi transfer" 
                                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 font-sans text-sm">
                                            @error('sender_name')
                                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-2">
                                        <button type="submit" 
                                                class="w-full py-3.5 px-6 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center justify-center gap-2">
                                            <span>📤</span>
                                            <span>Kirim Bukti Pembayaran</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Cancel Order Button (FR-13) -->
                            <div class="pt-2 text-center">
                                <form action="{{ route('orders.cancel', $order->booking_code) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Kuota tiket akan dikembalikan.');">
                                    @csrf
                                    <button type="submit" 
                                            class="text-xs font-bold text-rose-600 hover:text-rose-800 underline decoration-2 cursor-pointer">
                                        Batalkan Pesanan Ini
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endif

                    <!-- SECTION 2: STATUS REJECTED (UPLOAD ULANG) -->
                    @if ($order->status === \App\Enums\OrderStatus::Rejected)
                        <div class="space-y-6">
                            <!-- Alert Penolakan -->
                            <div class="p-5 sm:p-6 bg-rose-50 rounded-3xl border-3 border-rose-500 shadow-hard space-y-3">
                                <div class="flex items-start gap-3">
                                    <span class="text-3xl shrink-0">⚠️</span>
                                    <div>
                                        <h3 class="font-display text-xl text-rose-900 uppercase">
                                            Bukti Pembayaran Ditolak Panitia
                                        </h3>
                                        <p class="text-xs sm:text-sm font-sans text-rose-800 mt-1">
                                            Mohon maaf, bukti pembayaran yang Anda kirimkan belum dapat kami verifikasi dengan alasan berikut:
                                        </p>
                                    </div>
                                </div>

                                <div class="p-4 bg-white rounded-2xl border-2 border-rose-300 font-sans text-sm text-rose-950 font-bold">
                                    "{{ $order->latestPayment?->reject_reason ?? 'Bukti transfer tidak sesuai, blur, atau nominal tidak cocok.' }}"
                                </div>

                                <p class="text-xs text-rose-700 font-sans">
                                    Silakan periksa kembali mutasi atau struk transfer Anda dan unggah ulang bukti yang sah di bawah ini sebelum pesanan kedaluwarsa.
                                </p>
                            </div>

                            <!-- Form Upload Ulang -->
                            <div class="p-6 bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard">
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="text-2xl">🔄</span>
                                    <div>
                                        <h4 class="font-display text-xl text-hf-purple-900 uppercase">
                                            Unggah Ulang Bukti Pembayaran
                                        </h4>
                                        <p class="text-xs text-gray-600 font-sans">
                                            Total yang harus ditransfer: <strong class="text-hf-purple-900 font-black">Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong> ke {{ config('harfest.bank.name') }} ({{ config('harfest.bank.account_number') }})
                                        </p>
                                    </div>
                                </div>

                                <form action="{{ route('orders.payment', $order->booking_code) }}" 
                                      method="POST" 
                                      enctype="multipart/form-data" 
                                      class="space-y-4"
                                      x-data="{ 
                                          fileName: '', 
                                          isImage: false, 
                                          imagePreview: null,
                                          fileChosen(event) {
                                              const file = event.target.files[0];
                                              if (!file) return;
                                              this.fileName = file.name;
                                              this.isImage = file.type.startsWith('image/');
                                              if (this.isImage) {
                                                  const reader = new FileReader();
                                                  reader.onload = (e) => { this.imagePreview = e.target.result; };
                                                  reader.readAsDataURL(file);
                                              } else {
                                                  this.imagePreview = null;
                                              }
                                          }
                                      }">
                                    @csrf

                                    <div>
                                        <label for="proof_retry" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1.5">
                                            File Bukti Baru <span class="text-hf-pink-500">*</span>
                                        </label>
                                        
                                        <div class="relative border-3 border-dashed border-hf-purple-900/60 hover:border-hf-purple-900 rounded-2xl p-4 sm:p-6 text-center bg-purple-50/50 hover:bg-purple-50 transition-colors cursor-pointer"
                                             @click="$refs.retryInput.click()">
                                            <input type="file" 
                                                   id="proof_retry" 
                                                   name="proof" 
                                                   x-ref="retryInput" 
                                                   @change="fileChosen" 
                                                   accept=".jpg,.jpeg,.png,.pdf" 
                                                   class="hidden" 
                                                   required>

                                            <template x-if="!fileName">
                                                <div class="space-y-1">
                                                    <span class="text-3xl block">📎</span>
                                                    <span class="font-display text-sm text-hf-purple-900 block">
                                                        Pilih file bukti transfer baru
                                                    </span>
                                                    <span class="text-[11px] text-gray-500 font-sans block">
                                                        JPG, PNG, atau PDF hingga 2 MB
                                                    </span>
                                                </div>
                                            </template>

                                            <template x-if="fileName">
                                                <div class="space-y-2">
                                                    <template x-if="imagePreview">
                                                        <img :src="imagePreview" alt="Pratinjau Bukti" class="max-h-48 mx-auto rounded-xl border-2 border-hf-purple-900 shadow-sm object-contain">
                                                    </template>
                                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-hf-purple-900 text-hf-yellow-400 font-display text-xs rounded-xl border border-hf-purple-900">
                                                        <span>📄</span>
                                                        <span x-text="fileName" class="truncate max-w-xs"></span>
                                                        <span class="text-white hover:text-rose-300 ml-1 font-sans">✕ Ganti</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        @error('proof')
                                            <p class="text-xs text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="bank_name_retry" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                                Bank / Dompet Digital Pengirim
                                            </label>
                                            <input type="text" 
                                                   id="bank_name_retry" 
                                                   name="bank_name" 
                                                   value="{{ old('bank_name', $order->latestPayment?->bank_name) }}" 
                                                   placeholder="Misal: BCA, Mandiri, GoPay" 
                                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 font-sans text-sm">
                                            @error('bank_name')
                                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="sender_name_retry" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                                Nama Pemilik Rekening Pengirim
                                            </label>
                                            <input type="text" 
                                                   id="sender_name_retry" 
                                                   name="sender_name" 
                                                   value="{{ old('sender_name', $order->latestPayment?->sender_name) }}" 
                                                   placeholder="Nama sesuai mutasi transfer" 
                                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 font-sans text-sm">
                                            @error('sender_name')
                                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        <button type="submit" 
                                                class="w-full py-3.5 px-6 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center justify-center gap-2">
                                            <span>🔄</span>
                                            <span>Unggah Ulang Bukti Pembayaran</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- SECTION 3: STATUS WAITING_VERIFICATION & OTHERS -->
                    @if ($order->status === \App\Enums\OrderStatus::WaitingVerification)
                        <div class="p-6 bg-blue-50 text-blue-950 rounded-3xl border-3 border-hf-blue-400 shadow-hard space-y-4">
                            <div class="flex items-start gap-4">
                                <span class="text-4xl shrink-0">⏳</span>
                                <div class="space-y-1">
                                    <h4 class="font-display text-xl text-blue-900 uppercase">
                                        Bukti Pembayaran Sedang Diverifikasi
                                    </h4>
                                    <p class="text-xs sm:text-sm font-sans text-blue-800">
                                        Terima kasih! Bukti transfer Anda telah tersimpan dan sedang menunggu peninjauan oleh tim panitia Harfest.
                                    </p>
                                </div>
                            </div>

                            @if ($order->latestPayment)
                                <div class="mt-3 p-4 bg-white rounded-2xl border-2 border-blue-200 text-xs sm:text-sm space-y-2">
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-2">
                                        <span class="text-gray-500 font-sans">Waktu Pengunggahan:</span>
                                        <span class="font-bold text-blue-900">{{ format_wib($order->latestPayment->created_at) }}</span>
                                    </div>
                                    @if ($order->latestPayment->bank_name || $order->latestPayment->sender_name)
                                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-2">
                                            <span class="text-gray-500 font-sans">Bank / Pengirim:</span>
                                            <span class="font-bold text-blue-900">
                                                {{ $order->latestPayment->bank_name ?? '-' }} (a.n. {{ $order->latestPayment->sender_name ?? '-' }})
                                            </span>
                                        </div>
                                    @endif
                                    <div class="pt-1 flex items-center justify-between">
                                        <span class="text-gray-500 font-sans">File Bukti Terkirim:</span>
                                        <a href="{{ route('files.payment', $order->latestPayment->id) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-hf-purple-900 text-hf-yellow-400 rounded-xl text-xs font-display border border-hf-purple-900 hover:bg-hf-purple-800">
                                            <span>📄 Lihat Bukti Bayar</span>
                                            <span>↗</span>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <p class="text-xs text-blue-700 font-sans italic">
                                *Verifikasi biasanya membutuhkan waktu maksimal 1x24 jam kerja. Status akan otomatis berubah setelah panitia memeriksa transaksi.
                            </p>
                        </div>
                    @elseif ($order->status === \App\Enums\OrderStatus::Paid)
                        <div class="p-6 bg-emerald-50 text-emerald-950 rounded-3xl border-3 border-emerald-500 shadow-hard space-y-4">
                            <div class="flex items-start gap-4">
                                <span class="text-4xl shrink-0">🎉</span>
                                <div>
                                    <h4 class="font-display text-xl text-emerald-900 uppercase">
                                        Pembayaran Terkonfirmasi Lunas!
                                    </h4>
                                    <p class="text-xs sm:text-sm font-sans text-emerald-800 mt-1">
                                        Selamat! Pesanan Anda telah diverifikasi oleh panitia dan tiket resmi Harfest telah terbit.
                                    </p>
                                </div>
                            </div>

                            @if ($order->latestPayment)
                                <div class="p-3.5 bg-white rounded-2xl border-2 border-emerald-200 text-xs flex items-center justify-between">
                                    <span class="text-gray-600 font-sans">Bukti Bayar Disetujui:</span>
                                    <a href="{{ route('files.payment', $order->latestPayment->id) }}" 
                                       target="_blank" 
                                       class="font-display text-emerald-800 hover:underline inline-flex items-center gap-1">
                                        <span>Lihat Dokumen</span>
                                        <span>↗</span>
                                    </a>
                                </div>
                            @endif

                            <!-- E-Ticket Button (FR-11, FR-12) -->
                            <div class="pt-2 flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('orders.tickets', $order->booking_code) }}" 
                                   class="inline-flex items-center justify-center gap-2.5 flex-1 py-4 px-6 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press">
                                    <span>🎟️</span>
                                    <span>Lihat E-Tiket QR ({{ $order->tickets()->count() }} Tiket)</span>
                                </a>
                                <a href="{{ route('orders.tickets.download', $order->booking_code) }}" 
                                   class="inline-flex items-center justify-center gap-2 py-4 px-6 bg-emerald-500 hover:bg-emerald-600 text-white font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press">
                                    <span>📥</span>
                                    <span>Unduh PDF</span>
                                </a>
                            </div>
                        </div>
                    @elseif ($order->status === \App\Enums\OrderStatus::Cancelled)
                        <div class="p-5 bg-gray-100 text-gray-800 rounded-2xl border-3 border-gray-400 shadow-hard-sm flex items-center gap-3">
                            <span class="text-3xl">🚫</span>
                            <div>
                                <h4 class="font-display text-lg uppercase">Pesanan Dibatalkan</h4>
                                <p class="text-xs sm:text-sm font-sans text-gray-600 mt-0.5">
                                    Pesanan ini telah dibatalkan dan kuota tiket telah dikembalikan ke sistem.
                                </p>
                            </div>
                        </div>
                    @elseif ($order->status === \App\Enums\OrderStatus::Expired)
                        <div class="p-5 bg-gray-100 text-gray-800 rounded-2xl border-3 border-gray-400 shadow-hard-sm flex items-center gap-3">
                            <span class="text-3xl">⌛</span>
                            <div>
                                <h4 class="font-display text-lg uppercase">Pesanan Kedaluwarsa</h4>
                                <p class="text-xs sm:text-sm font-sans text-gray-600 mt-0.5">
                                    Batas waktu pembayaran 24 jam telah berakhir. Silakan lakukan pemesanan ulang.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- SECTION 3: RINCIAN TIKET & PESANAN -->
                    <div>
                        <h3 class="font-display text-xl text-hf-purple-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                            <span>📋</span>
                            <span>Rincian Pesanan</span>
                        </h3>

                        <div class="border-3 border-hf-purple-900 rounded-2xl overflow-hidden">
                            <table class="w-full text-left font-sans text-sm">
                                <thead class="bg-hf-purple-900 text-white font-display text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="p-3.5">Kategori Tiket</th>
                                        <th class="p-3.5 text-center">Harga</th>
                                        <th class="p-3.5 text-center">Jumlah</th>
                                        <th class="p-3.5 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="p-3.5">
                                            <strong class="font-bold text-hf-purple-900 block">{{ $order->ticketType->name }}</strong>
                                            <span class="text-xs text-gray-500">1 paket berlaku untuk {{ $order->ticketType->people_per_ticket }} orang</span>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            Rp{{ number_format($order->ticketType->price, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3.5 text-center font-bold">
                                            {{ $order->quantity }} paket
                                        </td>
                                        <td class="p-3.5 text-right font-display text-base text-hf-purple-900 font-bold">
                                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-purple-50 font-display font-bold">
                                    <tr>
                                        <td colspan="3" class="p-3.5 text-right uppercase text-xs tracking-wider">Total Tagihan:</td>
                                        <td class="p-3.5 text-right text-lg text-hf-purple-900">
                                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- SECTION 4: INFORMASI PEMESAN & CIVITAS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-purple-50 rounded-2xl border-2 border-purple-200 text-xs sm:text-sm font-sans space-y-1.5">
                            <h4 class="font-display text-sm text-hf-purple-900 uppercase mb-2">Informasi Pembeli:</h4>
                            <p>Nama: <strong>{{ $order->user->name }}</strong></p>
                            <p>Email: <strong>{{ $order->user->email }}</strong></p>
                            <p>No. WhatsApp: <strong>{{ $order->user->phone }}</strong></p>
                        </div>

                        @if ($order->student_id)
                            <div class="p-4 bg-pink-50 rounded-2xl border-2 border-pink-200 text-xs sm:text-sm font-sans space-y-2">
                                <h4 class="font-display text-sm text-hf-pink-500 uppercase">Identitas Civitas:</h4>
                                <p>NIM / NIP: <strong>{{ $order->student_id }}</strong></p>
                                @if ($order->student_card_path)
                                    <div>
                                        <a href="{{ route('files.student-card', $order->booking_code) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1 bg-hf-pink-500 text-white rounded-xl text-xs font-display hover:bg-hf-pink-400">
                                            <span>📷 Lihat Foto KTM</span>
                                            <span>↗</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
