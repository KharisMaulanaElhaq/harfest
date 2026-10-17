<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>

    <div class="py-12 bg-hf-pattern min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <x-section-title>PENGATURAN PROFIL</x-section-title>
                    <p class="text-purple-200 font-sans text-sm sm:text-base mt-2">
                        Kelola informasi akun, kontak, dan kata sandi masuk Harfest kamu.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <x-mascot type="blob" size="sm" />
                </div>
            </div>

            <!-- Profile Info Card -->
            <div class="p-6 sm:p-8 bg-white border-4 border-hf-purple-900 rounded-3xl shadow-hard text-hf-purple-900">
                <div class="max-w-xl">
                    <h3 class="font-display text-2xl uppercase text-hf-purple-900 mb-1">
                        Informasi Pengguna
                    </h3>
                    <p class="text-sm text-gray-600 mb-6 font-medium">
                        Perbarui nama lengkap dan alamat email yang terhubung dengan tiketmu.
                    </p>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="p-6 sm:p-8 bg-white border-4 border-hf-purple-900 rounded-3xl shadow-hard text-hf-purple-900">
                <div class="max-w-xl">
                    <h3 class="font-display text-2xl uppercase text-hf-purple-900 mb-1">
                        Ubah Kata Sandi
                    </h3>
                    <p class="text-sm text-gray-600 mb-6 font-medium">
                        Pastikan akunmu tetap aman dengan menggunakan kata sandi yang kuat.
                    </p>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete User Card -->
            <div class="p-6 sm:p-8 bg-white border-4 border-hf-purple-900 rounded-3xl shadow-hard text-hf-purple-900">
                <div class="max-w-xl">
                    <h3 class="font-display text-2xl uppercase text-red-600 mb-1">
                        Hapus Akun
                    </h3>
                    <p class="text-sm text-gray-600 mb-6 font-medium">
                        Setelah akun dihapus, seluruh data dan pesanan tiket akan dihapus secara permanen.
                    </p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
