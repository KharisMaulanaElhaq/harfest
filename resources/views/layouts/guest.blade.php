<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - Harfest' : 'Masuk / Daftar - Harfest 2026' }}</title>

    <!-- Google Fonts: Montserrat & Lilita One -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600;1,700&family=Lilita+One&display=swap" rel="stylesheet">

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-hf-pattern text-white min-h-screen flex flex-col justify-center items-center p-4 relative overflow-x-hidden">
    <!-- Back to Home Link -->
    <div class="w-full max-w-md mb-4 flex justify-between items-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-bold text-hf-yellow-400 hover:text-white transition-colors bg-hf-purple-900/80 px-4 py-1.5 rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
            <span>← Kembali ke Beranda</span>
        </a>
    </div>

    <!-- Container with Mascots -->
    <div class="w-full max-w-md relative">
        <!-- Floating Retro Device Mascot (Top-Left) -->
        <div class="absolute -top-12 -left-8 md:-left-12 z-20 pointer-events-none">
            <x-mascot type="retro" size="md" />
        </div>

        <!-- Floating Blob Mascot (Top-Right) -->
        <div class="absolute -top-14 -right-6 md:-right-10 z-20 pointer-events-none">
            <x-mascot type="blob" size="md" />
        </div>

        <!-- Main Card Container -->
        <div class="w-full bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg p-6 sm:p-8 relative z-10">
            <!-- Logo Header inside Card -->
            <div class="flex flex-col items-center mb-6">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/brand/harfest-logo.png') }}" alt="Harfest Logo 2026" class="h-16 w-auto object-contain transform hover:scale-105 transition-transform drop-shadow-[2px_2px_0_#4D0570]">
                </a>
            </div>

            {{ $slot }}
        </div>
    </div>

    <!-- Small Footer Note -->
    <div class="mt-6 text-center text-xs text-purple-200 font-sans">
        <p>© {{ date('Y') }} Harfest Ticketing System • Universitas Harkat Negeri</p>
    </div>
</body>
</html>
