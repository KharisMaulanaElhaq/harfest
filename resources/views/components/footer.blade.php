<footer class="bg-hf-purple-900 border-t-4 border-hf-purple-900 text-purple-100 relative overflow-hidden">
    <!-- Top Pattern Bar -->
    <x-pattern-divider variant="pink" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            <!-- Brand & Mascots -->
            <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-4">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/brand/harfest-logo.png') }}" alt="Harfest Logo" class="h-14 w-auto object-contain drop-shadow-[2px_2px_0_#4D0570]">
                </a>
                <p class="text-sm font-sans text-purple-200 max-w-sm leading-relaxed">
                    <strong>Harkat Negeri Fest (Harfest) 2026</strong> adalah panggung pesta musik kampus tahunan Universitas Harkat Negeri in collaboration with <strong>Jendela Creative Corps</strong>. Rayakan keberagaman karya dan energi anak muda!
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <x-mascot type="blob" size="sm" />
                    <x-mascot type="retro" size="sm" />
                </div>
            </div>

            <!-- Quick Nav -->
            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <h4 class="font-display text-xl text-hf-yellow-400 uppercase tracking-wider mb-4">
                    Navigasi Event
                </h4>
                <ul class="space-y-2 text-sm font-sans font-medium">
                    <li><a href="{{ url('/') }}" class="hover:text-hf-yellow-400 transition-colors">✦ Beranda Utama</a></li>
                    <li><a href="{{ url('/#lineup') }}" class="hover:text-hf-yellow-400 transition-colors">✦ Lineup Bintang Tamu</a></li>
                    <li><a href="{{ url('/#tiket') }}" class="hover:text-hf-yellow-400 transition-colors">✦ Informasi & Beli Tiket</a></li>
                    <li><a href="{{ url('/orders') }}" class="hover:text-hf-yellow-400 transition-colors">✦ Cek Status Pesanan</a></li>
                </ul>
            </div>

            <!-- Panitia & Bantuan -->
            <div class="flex flex-col items-center md:items-start text-center md:text-left">
                <h4 class="font-display text-xl text-hf-yellow-400 uppercase tracking-wider mb-4">
                    Pusat Informasi & Bantuan
                </h4>
                <p class="text-sm text-purple-200 mb-3">
                    Butuh bantuan konfirmasi transfer atau tiket? Hubungi panitia via WhatsApp resmi:
                </p>
                <a href="https://wa.me/{{ config('harfest.contact_whatsapp') }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm font-display text-sm tracking-wide btn-press hover:bg-emerald-600">
                    <span>💬 WhatsApp Panitia</span>
                    <span class="text-xs bg-emerald-700 px-2 py-0.5 rounded-full font-sans">{{ config('harfest.contact_whatsapp') }}</span>
                </a>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="mt-10 pt-6 border-t-2 border-purple-800/60 flex flex-col sm:flex-row items-center justify-between text-xs text-purple-300 font-sans gap-3">
            <p>© {{ date('Y') }} Harfest - Universitas Harkat Negeri. All rights reserved.</p>
            <p class="font-bold text-hf-yellow-400">Created with 💜 for Harfest Campus Concert</p>
        </div>
    </div>
</footer>
