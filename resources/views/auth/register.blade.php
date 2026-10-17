<x-guest-layout>
    <x-slot:title>Daftar Akun Baru</x-slot:title>

    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl sm:text-3xl text-hf-purple-900 uppercase tracking-wide">
            Daftar Akun
        </h1>
        <p class="text-xs sm:text-sm text-gray-600 font-sans mt-1">
            Buat akun untuk memesan tiket konser Harfest 2026
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama sesuai identitas" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="email@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" value="Nomor WhatsApp / HP" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi (Min. 8 Karakter)" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" 
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Ulangi Kata Sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" 
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm font-bold text-rose-600" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Daftar Sekarang 🎉
            </x-primary-button>
        </div>

        <div class="pt-3 text-center border-t-2 border-gray-100">
            <p class="text-xs sm:text-sm font-sans text-gray-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-hf-pink-500 hover:text-hf-purple-900 underline decoration-2">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
