<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Dilarang • Harfest 2026</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-hf-pattern text-white min-h-screen flex flex-col justify-center items-center p-4 relative overflow-x-hidden">
    <div class="w-full max-w-lg text-center relative z-10">
        <!-- Floating Mascot -->
        <div class="flex justify-center mb-6">
            <div class="relative inline-block animate-bounce" style="animation-duration: 2.5s;">
                <x-mascot type="retro" size="lg" />
                <span class="absolute -top-3 -right-4 bg-hf-pink-500 text-white font-display text-sm px-3 py-1 rounded-full border-2 border-hf-purple-900 shadow-hard-sm uppercase rotate-12">
                    Eits, Mau ke mana?
                </span>
            </div>
        </div>

        <!-- Card Container -->
        <div class="bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg p-6 sm:p-10 relative">
            <h1 class="font-display text-5xl sm:text-6xl text-hf-pink-500 tracking-wide drop-shadow-[3px_3px_0_#2D0A4E] mb-2">
                403
            </h1>
            <h2 class="font-display text-2xl sm:text-3xl text-hf-purple-900 mb-4 uppercase">
                Akses Dibatasi!
            </h2>
            <p class="text-gray-700 text-sm sm:text-base leading-relaxed mb-6 font-medium">
                {{ $exception->getMessage() ?: 'Kamu tidak memiliki izin untuk membuka halaman atau berkas ini. Pastikan kamu sudah masuk dengan akun yang sesuai.' }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center font-display uppercase tracking-wide text-sm sm:text-base px-6 py-3 rounded-2xl bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 shadow-hard hover:shadow-hard-sm hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                    🏠 Kembali ke Beranda
                </a>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center font-display uppercase tracking-wide text-sm sm:text-base px-6 py-3 rounded-2xl bg-hf-pink-500 text-white border-3 border-hf-purple-900 shadow-hard hover:shadow-hard-sm hover:translate-x-[2px] hover:translate-y-[2px] transition-all">
                    🎟️ Pesanan Saya
                </a>
            </div>
        </div>

        <p class="mt-6 text-xs text-purple-200">
            Harfest 2026 • Universitas Harkat Negeri
        </p>
    </div>
</body>
</html>
