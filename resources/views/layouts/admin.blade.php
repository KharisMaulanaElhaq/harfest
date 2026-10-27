<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - Harfest Admin' : 'Admin Panel - Harfest 2026' }}</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts: Montserrat & Lilita One -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600;1,700&family=Lilita+One&display=swap" rel="stylesheet">

    <!-- Vite Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 selection:bg-hf-yellow-400 selection:text-hf-purple-900" 
      x-data="{ sidebarOpen: false }">

    @php
        $pendingPaymentsCount = 0;
        if (Gate::allows('is-admin')) {
            $pendingPaymentsQuery = \App\Models\Payment::where('status', \App\Enums\PaymentStatus::Pending);
            if (isset($currentAdminEvent) && $currentAdminEvent) {
                $pendingPaymentsQuery->whereHas('order', fn($q) => $q->where('event_id', $currentAdminEvent->id));
            }
            $pendingPaymentsCount = $pendingPaymentsQuery->count();
        }
    @endphp

    <div class="min-h-full flex flex-col md:flex-row">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden"
             style="display: none;"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-hf-purple-900 text-white flex flex-col justify-between border-r-4 border-hf-purple-950 transition-transform duration-300 ease-in-out md:static md:translate-x-0 shrink-0 shadow-2xl md:shadow-none">
            
            <div>
                <!-- Brand Header -->
                <div class="p-4 sm:p-5 border-b-4 border-hf-purple-950 bg-hf-purple-950/60 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/brand/harfest-logo.png') }}" alt="Harfest Admin" class="h-10 w-auto object-contain drop-shadow-[1px_1px_0_#4D0570]">
                        <div>
                            <span class="font-sans text-[11px] text-hf-pink-400 uppercase tracking-widest font-black block">Admin Panel</span>
                        </div>
                    </a>

                    <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-hf-yellow-400 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Event Switcher in Sidebar -->
                <div class="p-3 border-b-2 border-hf-purple-950/80 bg-hf-purple-950/40">
                    <x-event-switcher :currentEvent="$currentAdminEvent ?? null" :events="$allAdminEvents ?? []" />
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 font-display text-sm tracking-wide">
                    
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">📊</span>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    @can('is-admin')
                    <!-- Verifikasi Pembayaran -->
                    <a href="{{ route('admin.payments.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">💳</span>
                            <span>Verifikasi Bayar</span>
                        </div>
                        @if ($pendingPaymentsCount > 0)
                            <span class="px-2 py-0.5 text-xs font-sans font-black rounded-full {{ request()->routeIs('admin.payments.*') ? 'bg-hf-purple-900 text-hf-yellow-400' : 'bg-hf-pink-500 text-white animate-pulse' }}">
                                {{ $pendingPaymentsCount }}
                            </span>
                        @endif
                    </a>
                    @endcan

                    <!-- Data Pesanan & Ekspor -->
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">📦</span>
                            <span>Data Pesanan</span>
                        </div>
                    </a>

                    @can('is-admin')
                    <!-- Kelola Event -->
                    <a href="{{ route('admin.events.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.events.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🎪</span>
                            <span>Kelola Event</span>
                        </div>
                    </a>

                    <!-- Kategori Tiket -->
                    <a href="{{ route('admin.ticket-types.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.ticket-types.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🎟️</span>
                            <span>Kategori Tiket</span>
                        </div>
                    </a>

                    <!-- Lineup Band -->
                    <a href="{{ route('admin.bands.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.bands.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🎸</span>
                            <span>Lineup Band</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Scanner Check-in (FR-20) -->
                    <a href="{{ route('admin.checkin.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border-2 transition-all {{ request()->routeIs('admin.checkin.*') ? 'bg-hf-yellow-400 text-hf-purple-900 border-hf-purple-950 shadow-hard-sm font-black' : 'border-transparent text-purple-200 hover:bg-hf-purple-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">📷</span>
                            <span>Check-in Scanner</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer (Admin Info & Links) -->
            <div class="p-4 border-t-4 border-hf-purple-950 bg-hf-purple-950/40 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-hf-yellow-400 text-hf-purple-900 font-display text-lg font-bold flex items-center justify-center border-2 border-hf-purple-950 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <span class="font-display text-xs text-white truncate block">{{ Auth::user()->name }}</span>
                        <span class="inline-block px-1.5 py-0.2 {{ Auth::user()->isStaff() ? 'bg-amber-600' : 'bg-hf-pink-500' }} text-white font-sans text-[10px] font-bold rounded">
                            {{ Auth::user()->isStaff() ? 'STAFF' : 'ADMIN' }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 flex flex-col gap-1.5 text-xs font-sans">
                    <a href="{{ route('home') }}" target="_blank" class="text-purple-200 hover:text-hf-yellow-400 flex items-center gap-1.5">
                        <span>🌐 Lihat Web Publik</span>
                        <span class="text-[10px]">↗</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" class="text-rose-400 hover:text-rose-300 flex items-center gap-1.5 font-bold cursor-pointer">
                            <span>🚪 Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Admin Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Bar -->
            <header class="bg-white border-b-4 border-slate-200 shadow-sm px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" 
                            class="md:hidden p-2 rounded-xl bg-hf-purple-900 text-hf-yellow-400 border-2 border-hf-purple-950">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="font-display text-xl sm:text-2xl text-hf-purple-900 uppercase tracking-wide">
                        {{ $header ?? 'Panel Verifikasi Panitia' }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 border-slate-300 hover:border-hf-purple-900 text-xs font-display text-slate-700 hover:text-hf-purple-900 transition-colors">
                        <span>Lihat Web</span>
                        <span>↗</span>
                    </a>

                    <div class="px-2.5 py-1 {{ Auth::user()->isStaff() ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-purple-100 text-purple-900 border-purple-300' }} text-xs font-display font-bold rounded-xl border">
                        {{ Auth::user()->isStaff() ? 'STAFF' : 'ADMIN' }}
                    </div>
                </div>
            </header>

            <!-- Notification Messages -->
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 p-4 bg-emerald-50 border-3 border-emerald-500 rounded-2xl shadow-hard-sm text-emerald-950 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">✅</span>
                        <span class="font-sans font-bold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4 p-4 bg-rose-50 border-3 border-rose-500 rounded-2xl shadow-hard-sm text-rose-950 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">⚠️</span>
                        <span class="font-sans font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Page Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                {{ $slot }}
            </main>

            <!-- Admin Footer -->
            <footer class="bg-white border-t-2 border-slate-200 px-6 py-3 text-center text-xs text-slate-500 font-sans">
                Harfest Ticketing System &copy; {{ date('Y') }} — Universitas Harkat Negeri
            </footer>
        </div>

    </div>

</body>
</html>
