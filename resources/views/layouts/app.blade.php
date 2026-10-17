<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - Harfest' : 'Harfest 2026 - Harkat Negeri Fest' }}</title>
    <meta name="description" content="Pemesanan tiket online konser kampus Harfest Universitas Harkat Negeri. Pilih tiketmu sekarang!">

    <!-- Google Fonts: Montserrat (Weights 500, 600, 700, 800, 900) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600;1,700;1,800;1,900&family=Lilita+One&display=swap" rel="stylesheet">

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-hf-magenta text-white selection:bg-hf-yellow-400 selection:text-hf-purple-900 min-h-screen flex flex-col">
    <!-- Navbar -->
    <x-navbar />

    <!-- Flash Notifications -->
    @if (session('success') || session('status'))
        <div class="max-w-4xl mx-auto px-4 mt-6 w-full">
            <div class="p-4 bg-emerald-400 text-emerald-950 font-bold border-4 border-hf-purple-900 rounded-2xl shadow-hard flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎉</span>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-4xl mx-auto px-4 mt-6 w-full">
            <div class="p-4 bg-rose-500 text-white font-bold border-4 border-hf-purple-900 rounded-2xl shadow-hard flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />
</body>
</html>
