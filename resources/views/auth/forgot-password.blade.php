<x-guest-layout>
    <x-slot name="title">Lupa Kata Sandi</x-slot>

    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl sm:text-3xl text-hf-purple-900 uppercase">
            Lupa Kata Sandi?
        </h2>
        <p class="mt-2 text-sm text-gray-600 font-medium">
            Tidak masalah! Masukkan email akunmu dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 mt-6">
            <x-primary-button class="w-full">
                Kirim Tautan Reset Kata Sandi
            </x-primary-button>

            <a href="{{ route('login') }}" class="text-center font-display text-sm text-hf-pink-500 hover:text-hf-purple-900 transition-colors py-2">
                ← Kembali ke Halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
