<x-admin-layout>
    <x-slot:title>Gate Check-in Scanner - Harfest Admin</x-slot:title>
    <x-slot:header>Gate Check-in Scanner</x-slot:header>

    <div class="space-y-6" 
         x-data="{
            activeTab: 'camera', // 'camera' or 'manual'
            ticketCode: '',
            isProcessing: false,
            selectedEventId: '{{ $selectedEvent?->id }}',
            selectedEventName: '{{ addslashes($selectedEvent?->name ?? 'Harfest') }}',
            lastResult: {{ session('checkin_result') ? json_encode(session('checkin_result')) : 'null' }},
            history: [],
            scannerRunning: false,
            scanner: null,
            audioEnabled: true,

            init() {
                if (this.lastResult) {
                    this.addToHistory(this.lastResult);
                }
                this.$nextTick(() => {
                    if (this.activeTab === 'camera') {
                        this.initScanner();
                    }
                });
            },

            playAudio(type) {
                if (!this.audioEnabled) return;
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    if (type === 'success') {
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                        osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5
                        gain.gain.setValueAtTime(0.3, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    } else {
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(220, ctx.currentTime);
                        osc.frequency.setValueAtTime(160, ctx.currentTime + 0.15);
                        gain.gain.setValueAtTime(0.3, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.4);
                    }
                } catch(e) {
                    // Audio context error, ignore
                }
            },

            initScanner() {
                if (typeof Html5Qrcode === 'undefined') {
                    console.warn('Html5Qrcode library not loaded yet');
                    return;
                }
                if (this.scannerRunning) return;

                const qrReaderElement = document.getElementById('qr-reader');
                if (!qrReaderElement) return;

                this.scanner = new Html5Qrcode('qr-reader');
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                this.scanner.start(
                    { facingMode: 'environment' }, 
                    config, 
                    (decodedText) => {
                        if (!this.isProcessing) {
                            this.verifyCode(decodedText);
                        }
                    },
                    (errorMessage) => {
                        // ignore scan frames without QR
                    }
                ).then(() => {
                    this.scannerRunning = true;
                }).catch((err) => {
                    console.error('Failed to start camera scanner:', err);
                    this.scannerRunning = false;
                });
            },

            stopScanner() {
                if (this.scanner && this.scannerRunning) {
                    this.scanner.stop().then(() => {
                        this.scannerRunning = false;
                    }).catch(err => console.error(err));
                }
            },

            switchTab(tab) {
                this.activeTab = tab;
                if (tab === 'camera') {
                    this.$nextTick(() => this.initScanner());
                } else {
                    this.stopScanner();
                    this.$nextTick(() => {
                        const input = document.getElementById('manual_code_input');
                        if (input) input.focus();
                    });
                }
            },

            verifyCode(code) {
                const cleanCode = (code || '').trim();
                if (!cleanCode || this.isProcessing) return;

                this.isProcessing = true;

                fetch('{{ route('admin.checkin.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        ticket_code: cleanCode,
                        event_id: this.selectedEventId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.lastResult = data;
                    this.lastResult.code = cleanCode;
                    this.addToHistory(this.lastResult);

                    if (data.result === 'success') {
                        this.playAudio('success');
                    } else {
                        this.playAudio('error');
                    }

                    this.ticketCode = '';
                    setTimeout(() => {
                        this.isProcessing = false;
                    }, 1500); // 1.5s cooldown before next scan
                })
                .catch(err => {
                    console.error('Verify error:', err);
                    this.lastResult = {
                        result: 'invalid',
                        message: 'Terjadi kesalahan sistem atau batas pemindaian (rate limit). Silakan coba lagi.',
                        code: cleanCode
                    };
                    this.playAudio('error');
                    this.isProcessing = false;
                });
            },

            addToHistory(item) {
                this.history.unshift({
                    code: item.code || (item.ticket ? item.ticket.ticket_code : '-'),
                    holder: item.holder_name || (item.ticket ? item.ticket.holder_name : '-'),
                    type: item.ticket_type || '-',
                    event: item.event_name || this.selectedEventName,
                    result: item.result,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                });
                if (this.history.length > 20) this.history.pop();
            }
         }">

        <!-- Top Overview & Mode Switcher -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                    Pemeriksaan Masuk Tiket Konser
                </h2>
                <div class="flex items-center gap-2 pt-1">
                    <label for="checkin_event_selector" class="text-xs font-sans font-bold text-slate-600">Event Pemeriksaan:</label>
                    <select id="checkin_event_selector" 
                            x-model="selectedEventId" 
                            @change="selectedEventName = $event.target.options[$event.target.selectedIndex].text"
                            class="text-xs px-2.5 py-1 rounded-lg border-2 border-hf-purple-900 font-sans font-black bg-hf-yellow-400 text-hf-purple-900 focus:outline-none">
                        @foreach ($allEvents as $ev)
                            <option value="{{ $ev->id }}" {{ $selectedEvent && $selectedEvent->id === $ev->id ? 'selected' : '' }}>
                                {{ $ev->name }} (Vol. {{ $ev->edition }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Sound Toggle -->
                <button type="button" 
                        @click="audioEnabled = !audioEnabled" 
                        class="px-3 py-1.5 rounded-xl border-2 text-xs font-display flex items-center gap-1.5 transition-colors"
                        :class="audioEnabled ? 'bg-hf-purple-100 text-hf-purple-900 border-hf-purple-900' : 'bg-slate-100 text-slate-400 border-slate-300'">
                    <span x-text="audioEnabled ? '🔔 Suara Aktif' : '🔕 Bisu'"></span>
                </button>

                <!-- Mode Switcher Tabs -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-300 text-xs font-display">
                    <button type="button" 
                            @click="switchTab('camera')" 
                            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"
                            :class="activeTab === 'camera' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900'">
                        <span>📷</span>
                        <span>Kamera Scanner</span>
                    </button>

                    <button type="button" 
                            @click="switchTab('manual')" 
                            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"
                            :class="activeTab === 'manual' ? 'bg-hf-purple-900 text-hf-yellow-400 shadow-xs' : 'text-slate-600 hover:text-hf-purple-900'">
                        <span>⌨️</span>
                        <span>Input Manual</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MAIN SCANNING & RESULT GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT: SCANNER / MANUAL INPUT BOX (5 Cols) -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- CAMERA SCANNER CARD -->
                <div x-show="activeTab === 'camera'" 
                     class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 space-y-4">
                    
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-3">
                        <span class="font-display text-sm text-hf-purple-900 uppercase flex items-center gap-2">
                            <span>📷</span>
                            <span>Kamera Scanner Aktif</span>
                        </span>

                        <template x-if="scannerRunning">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Memindai...
                            </span>
                        </template>
                    </div>

                    <!-- Video Viewfinder Container -->
                    <div class="relative bg-black rounded-2xl overflow-hidden border-2 border-hf-purple-900 aspect-square flex items-center justify-center">
                        <div id="qr-reader" class="w-full h-full"></div>

                        <!-- Processing overlay -->
                        <div x-show="isProcessing" 
                             class="absolute inset-0 bg-hf-purple-900/80 backdrop-blur-xs flex flex-col items-center justify-center text-white space-y-2 z-20">
                            <span class="text-3xl animate-spin">⏳</span>
                            <span class="font-display text-sm uppercase text-hf-yellow-400">Memeriksa Tiket...</span>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button type="button" 
                                @click="initScanner()" 
                                x-show="!scannerRunning"
                                class="flex-1 py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                            Nyalakan Kamera
                        </button>
                        <button type="button" 
                                @click="stopScanner()" 
                                x-show="scannerRunning"
                                class="flex-1 py-2.5 px-4 bg-slate-200 hover:bg-slate-300 text-slate-800 font-display text-xs uppercase rounded-xl border border-slate-400">
                            Matikan Kamera
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500 font-sans text-center">
                        Pastikan izin kamera browser telah diaktifkan dan QR tiket berada di dalam bingkai pemindaian.
                    </p>
                </div>

                <!-- MANUAL INPUT CARD -->
                <div x-show="activeTab === 'manual'" 
                     class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-4">
                    
                    <div class="border-b-2 border-slate-100 pb-3">
                        <span class="font-display text-base text-hf-purple-900 uppercase flex items-center gap-2">
                            <span>⌨️</span>
                            <span>Ketik Kode Tiket Manual</span>
                        </span>
                        <p class="text-xs text-slate-500 font-sans mt-0.5">
                            Gunakan jika kamera bermasalah atau barcode pengunjung sulit terbaca.
                        </p>
                    </div>

                    <form @submit.prevent="verifyCode(ticketCode)" class="space-y-4">
                        <div>
                            <label for="manual_code_input" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1.5">
                                Kode Tiket (HF-...) <span class="text-hf-pink-500">*</span>
                            </label>
                            <input type="text" 
                                   id="manual_code_input" 
                                   x-model="ticketCode"
                                   placeholder="HF-XXXXXXXXXXXXXXXX" 
                                   class="w-full px-4 py-3.5 rounded-2xl border-3 border-hf-purple-900 font-mono font-black text-lg tracking-wider uppercase text-hf-purple-900 placeholder:text-slate-300 focus:outline-none focus:ring-4 focus:ring-hf-yellow-400"
                                   autocomplete="off"
                                   required>
                        </div>

                        <button type="submit" 
                                :disabled="isProcessing || !ticketCode.trim()"
                                class="w-full py-4 px-6 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-base uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                            <span x-show="!isProcessing">🔍 Verifikasi & Check-in</span>
                            <span x-show="isProcessing" style="display: none;">Memeriksa...</span>
                        </button>
                    </form>

                    <div class="pt-2 text-center">
                        <span class="text-[11px] text-slate-400 font-sans">
                            Tekan <kbd class="px-1.5 py-0.5 bg-slate-100 border rounded font-mono text-[10px]">Enter</kbd> untuk verifikasi instan.
                        </span>
                    </div>
                </div>

            </div>

            <!-- RIGHT: BIG RESULT DISPLAY & HISTORY (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 1. BIG RESULT DISPLAY BOX -->
                <div>
                    <!-- IDLE STATE -->
                    <template x-if="!lastResult">
                        <div class="p-10 bg-white rounded-3xl border-4 border-dashed border-slate-300 text-center space-y-3">
                            <span class="text-6xl block opacity-60">🎯</span>
                            <h3 class="font-display text-2xl text-slate-400 uppercase tracking-wide">
                                Menunggu Pemindaian
                            </h3>
                            <p class="text-xs sm:text-sm font-sans text-slate-400 max-w-sm mx-auto">
                                Arahkan kamera ke QR e-tiket pengunjung atau masukkan kode tiket manual di samping untuk melihat hasil verifikasi.
                            </p>
                        </div>
                    </template>

                    <!-- A. SUCCESS RESULT (HIJAU BESAR) -->
                    <template x-if="lastResult && lastResult.result === 'success'">
                        <div class="p-6 sm:p-8 bg-emerald-500 text-white rounded-3xl border-4 border-hf-purple-950 shadow-hard-lg space-y-4 animate-in fade-in zoom-in-95 duration-200">
                            <div class="flex items-center gap-4">
                                <span class="text-6xl sm:text-7xl shrink-0">✅</span>
                                <div>
                                    <span class="px-3 py-1 bg-white text-emerald-800 font-display text-xs uppercase rounded-xl border border-emerald-700 font-black inline-block mb-1">
                                        TIKET VALID
                                    </span>
                                    <h3 class="font-display text-3xl sm:text-5xl text-white tracking-wide uppercase leading-none">
                                        CHECK-IN BERHASIL!
                                    </h3>
                                </div>
                            </div>

                            <div class="p-5 bg-white text-slate-900 rounded-2xl border-3 border-hf-purple-950 shadow-sm space-y-3 font-sans">
                                <div>
                                    <span class="text-xs text-gray-500 block uppercase font-bold tracking-wider">Nama Pemegang Tiket:</span>
                                    <span class="font-display text-2xl sm:text-3xl text-hf-purple-900 block" x-text="lastResult.holder_name"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Event:</span>
                                        <strong class="font-bold text-hf-purple-900 text-sm" x-text="lastResult.event_name || selectedEventName"></strong>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Kategori Tiket:</span>
                                        <strong class="font-bold text-slate-800 text-sm" x-text="lastResult.ticket_type"></strong>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Kode Booking:</span>
                                        <strong class="font-bold text-slate-800 text-sm" x-text="lastResult.booking_code"></strong>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Waktu Check-in:</span>
                                        <span class="font-bold text-emerald-700" x-text="new Date().toLocaleTimeString('id-ID') + ' WIB'"></span>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-emerald-100 font-sans text-center font-bold">
                                Silakan persilakan pengunjung masuk ke area konser.
                            </p>
                        </div>
                    </template>

                    <!-- B. WRONG EVENT RESULT (MERAH BESAR - TIKET UNTUK EVENT LAIN) -->
                    <template x-if="lastResult && lastResult.result === 'wrong_event'">
                        <div class="p-6 sm:p-8 bg-rose-600 text-white rounded-3xl border-4 border-hf-purple-950 shadow-hard-lg space-y-4 animate-in fade-in zoom-in-95 duration-200">
                            <div class="flex items-center gap-4">
                                <span class="text-6xl sm:text-7xl shrink-0">🚫</span>
                                <div>
                                    <span class="px-3 py-1 bg-white text-rose-900 font-display text-xs uppercase rounded-xl border border-rose-800 font-black inline-block mb-1">
                                        EVENT BERBEDA
                                    </span>
                                    <h3 class="font-display text-3xl sm:text-5xl text-white tracking-wide uppercase leading-none">
                                        TIKET UNTUK EVENT LAIN!
                                    </h3>
                                </div>
                            </div>

                            <div class="p-5 bg-white text-slate-900 rounded-2xl border-3 border-hf-purple-950 shadow-sm space-y-3 font-sans">
                                <div class="p-3 bg-rose-50 text-rose-950 rounded-xl border border-rose-300 font-bold text-xs sm:text-sm">
                                    <span x-text="lastResult.message"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Event Terdaftar Tiket:</span>
                                        <strong class="font-bold text-rose-700 text-sm" x-text="lastResult.event_name || '-'"></strong>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Gate Pemeriksaan Ini:</span>
                                        <strong class="font-bold text-hf-purple-900 text-sm" x-text="selectedEventName"></strong>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Nama Pemegang:</span>
                                        <strong class="font-bold text-slate-800" x-text="lastResult.holder_name"></strong>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Kode Booking:</span>
                                        <strong class="font-bold text-slate-800" x-text="lastResult.booking_code"></strong>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-rose-100 font-sans text-center font-bold">
                                Pengunjung memiliki tiket sah untuk edisi yang berbeda. Arahkan pengunjung ke edisi yang bersangkutan.
                            </p>
                        </div>
                    </template>

                    <!-- C. ALREADY USED RESULT (MERAH BESAR + WAKTU) -->
                    <template x-if="lastResult && lastResult.result === 'used'">
                        <div class="p-6 sm:p-8 bg-rose-600 text-white rounded-3xl border-4 border-hf-purple-950 shadow-hard-lg space-y-4 animate-in fade-in zoom-in-95 duration-200">
                            <div class="flex items-center gap-4">
                                <span class="text-6xl sm:text-7xl shrink-0">⛔</span>
                                <div>
                                    <span class="px-3 py-1 bg-white text-rose-800 font-display text-xs uppercase rounded-xl border border-rose-700 font-black inline-block mb-1">
                                        AKSES DITOLAK
                                    </span>
                                    <h3 class="font-display text-3xl sm:text-5xl text-white tracking-wide uppercase leading-none">
                                        TIKET SUDAH DIPAKAI!
                                    </h3>
                                </div>
                            </div>

                            <div class="p-5 bg-white text-slate-900 rounded-2xl border-3 border-hf-purple-950 shadow-sm space-y-3 font-sans">
                                <div class="p-3 bg-rose-50 text-rose-950 rounded-xl border border-rose-300 font-medium text-xs sm:text-sm">
                                    <span class="font-bold block text-rose-800 mb-0.5">⚠️ Riwayat Penggunaan:</span>
                                    <span x-text="lastResult.message"></span>
                                </div>

                                <div>
                                    <span class="text-xs text-gray-500 block uppercase font-bold tracking-wider">Nama Terdaftar:</span>
                                    <span class="font-display text-xl text-hf-purple-900 block" x-text="lastResult.holder_name"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Kategori:</span>
                                        <strong class="font-bold text-slate-800" x-text="lastResult.ticket_type"></strong>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block uppercase font-bold text-[10px]">Kode Booking:</span>
                                        <strong class="font-bold text-slate-800" x-text="lastResult.booking_code"></strong>
                                    </div>
                                </div>
                            </div>

                            <p class="text-xs text-rose-100 font-sans text-center font-bold">
                                PERINGATAN: Tiket ini tidak dapat digunakan dua kali. Tahan pengunjung jika terindikasi duplikasi.
                            </p>
                        </div>
                    </template>

                    <!-- D. INVALID RESULT (MERAH BESAR - TIDAK VALID) -->
                    <template x-if="lastResult && lastResult.result === 'invalid'">
                        <div class="p-6 sm:p-8 bg-rose-700 text-white rounded-3xl border-4 border-hf-purple-950 shadow-hard-lg space-y-4 animate-in fade-in zoom-in-95 duration-200">
                            <div class="flex items-center gap-4">
                                <span class="text-6xl sm:text-7xl shrink-0">❌</span>
                                <div>
                                    <span class="px-3 py-1 bg-white text-rose-900 font-display text-xs uppercase rounded-xl border border-rose-800 font-black inline-block mb-1">
                                        TIDAK VALID
                                    </span>
                                    <h3 class="font-display text-3xl sm:text-5xl text-white tracking-wide uppercase leading-none">
                                        TIKET TIDAK VALID!
                                    </h3>
                                </div>
                            </div>

                            <div class="p-5 bg-white text-slate-900 rounded-2xl border-3 border-hf-purple-950 shadow-sm space-y-2 font-sans">
                                <p class="text-sm font-bold text-rose-900" x-text="lastResult.message"></p>
                                <p class="text-xs text-gray-600">
                                    Kode yang diperiksa: <code class="font-mono bg-slate-100 px-2 py-0.5 rounded font-black text-rose-700" x-text="lastResult.code"></code>
                                </p>
                            </div>

                            <p class="text-xs text-rose-200 font-sans text-center font-bold">
                                Pastikan kode tiket berasal dari pemesanan resmi Harfest dan pembayaran telah lunas.
                            </p>
                        </div>
                    </template>
                </div>

                <!-- 2. RECENT CHECK-IN HISTORY FEED -->
                <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-5 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between border-b-2 border-slate-100 pb-3">
                        <span class="font-display text-base text-hf-purple-900 uppercase flex items-center gap-2">
                            <span>📋</span>
                            <span>Riwayat Pemindaian Sesi Ini</span>
                        </span>

                        <span class="text-xs text-slate-500 font-sans" x-text="history.length + ' scan'"></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left font-sans text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-display text-[11px] uppercase tracking-wider">
                                <tr>
                                    <th class="p-2.5">Waktu</th>
                                    <th class="p-2.5">Kode Tiket</th>
                                    <th class="p-2.5">Nama Pemegang</th>
                                    <th class="p-2.5">Event</th>
                                    <th class="p-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(item, idx) in history" :key="idx">
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-2.5 text-slate-500 font-mono" x-text="item.time"></td>
                                        <td class="p-2.5 font-mono font-bold text-hf-purple-900" x-text="item.code"></td>
                                        <td class="p-2.5 font-bold text-slate-800" x-text="item.holder"></td>
                                        <td class="p-2.5 text-slate-600 text-[11px]" x-text="item.event"></td>
                                        <td class="p-2.5 text-center">
                                            <template x-if="item.result === 'success'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    BERHASIL
                                                </span>
                                            </template>
                                            <template x-if="item.result === 'wrong_event'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                    EVENT LAIN
                                                </span>
                                            </template>
                                            <template x-if="item.result === 'used'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    SUDAH DIPAKAI
                                                </span>
                                            </template>
                                            <template x-if="item.result === 'invalid'">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-200 text-gray-800 border border-gray-400">
                                                    TIDAK VALID
                                                </span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="history.length === 0">
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-400 italic">
                                            Belum ada pemindaian tiket pada sesi ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-admin-layout>
