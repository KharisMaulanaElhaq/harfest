<x-guest-layout>
    <x-slot name="title">Verifikasi Email</x-slot>

    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl sm:text-3xl text-hf-purple-900 uppercase">
            Verifikasi Email
        </h2>
        <p class="mt-2 text-sm text-gray-600 font-medium leading-relaxed">
            Terima kasih telah mendaftar! Sebelum mulai memesan tiket, silakan verifikasi alamat email kamu dengan mengklik tautan yang baru saja kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-emerald-100 border-2 border-emerald-500 rounded-xl font-medium text-sm text-emerald-800 text-center">
            Tautan verifikasi baru telah dikirimkan ke email yang kamu daftarkan.
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="w-full">
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button type="submit" class="font-display text-sm text-hf-pink-500 hover:text-hf-purple-900 transition-colors py-2">
                ← Keluar dari Akun (Logout)
            </button>
        </form>
    </div>
</x-guest-layout>
