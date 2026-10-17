<x-guest-layout>
    <x-slot name="title">Konfirmasi Kata Sandi</x-slot>

    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl sm:text-3xl text-hf-purple-900 uppercase">
            Konfirmasi Sandi
        </h2>
        <p class="mt-2 text-sm text-gray-600 font-medium">
            Ini adalah area aman. Harap masukkan kembali kata sandi kamu sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Konfirmasi Kata Sandi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
