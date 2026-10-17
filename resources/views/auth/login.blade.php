<x-guest-layout>
    <x-slot:title>Masuk Akun</x-slot:title>

    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl sm:text-3xl text-hf-purple-900 uppercase tracking-wide">
            Masuk ke Akun
        </h1>
        <p class="text-xs sm:text-sm text-gray-600 font-sans mt-1">
            Lanjutkan pemesanan tiket konser Harfest 2026
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-sm font-bold text-emerald-600 bg-emerald-50 p-3 rounded-xl border-2 border-emerald-500" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Kata Sandi" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-hf-pink-500 hover:text-hf-purple-900 underline" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg border-2 border-hf-purple-900 text-hf-pink-500 shadow-sm focus:ring-hf-yellow-400" name="remember">
                <span class="ms-2 text-xs sm:text-sm font-bold text-hf-purple-900">Ingat Saya</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Masuk Sekarang 🔥
            </x-primary-button>
        </div>

        <div class="pt-3 text-center border-t-2 border-gray-100">
            <p class="text-xs sm:text-sm font-sans text-gray-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-hf-pink-500 hover:text-hf-purple-900 underline decoration-2">
                    Daftar di sini
                </a>
            </p>
        </div>

        <!-- Quick Demo Accounts Helper -->
        <div class="mt-4 p-3 bg-purple-50 rounded-2xl border-2 border-dashed border-hf-purple-500 text-xs font-sans text-hf-purple-900">
            <p class="font-bold mb-1 text-center">💡 Akun Demo Cepat:</p>
            <div class="flex flex-col gap-1 text-[11px]">
                <div class="flex justify-between">
                    <span>Admin: <strong class="select-all">admin@harfest.test</strong></span>
                    <span>Sandi: <strong class="select-all">password</strong></span>
                </div>
                <div class="flex justify-between">
                    <span>User: <strong class="select-all">user@harfest.test</strong></span>
                    <span>Sandi: <strong class="select-all">password</strong></span>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>
