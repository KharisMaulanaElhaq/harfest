<nav x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="sticky top-0 z-50 bg-hf-purple-900/95 backdrop-blur-md border-b-4 border-hf-purple-900 shadow-hard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <!-- Left: Logo (Official Harfest Logo: Globe + Harfest Plate + Player) -->
            <div class="flex items-center space-x-3">
                <a href="{{ url('/') }}" class="flex items-center group transform hover:scale-105 transition-transform duration-200">
                    <img src="{{ asset('images/brand/harfest-logo.png') }}" alt="Harfest 2026 Logo" class="h-11 sm:h-16 w-auto object-contain select-none">
                </a>
            </div>

            <!-- Center: Desktop Navigation (Montserrat 700/800 Extrabold) -->
            <div class="hidden md:flex items-center space-x-6 font-sans font-extrabold text-base tracking-wider uppercase">
                <a href="{{ url('/') }}" class="text-white hover:text-hf-yellow-400 transition-colors {{ request()->is('/') && !request()->has('page') ? 'text-hf-yellow-400 underline decoration-hf-pink-500 decoration-4 underline-offset-8' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('events.index') }}" class="text-white hover:text-hf-yellow-400 transition-colors {{ request()->routeIs('events.*') ? 'text-hf-yellow-400 underline decoration-hf-pink-500 decoration-4 underline-offset-8' : '' }}">
                    Event
                </a>
                <a href="{{ url('/#lineup') }}" class="text-white hover:text-hf-yellow-400 transition-colors">
                    Lineup
                </a>
                <a href="{{ url('/#tiket') }}" class="text-white hover:text-hf-yellow-400 transition-colors">
                    Tiket
                </a>
                @auth
                    <a href="{{ Route::has('orders.index') ? route('orders.index') : url('/orders') }}" class="text-white hover:text-hf-yellow-400 transition-colors {{ request()->is('orders*') ? 'text-hf-yellow-400 underline decoration-hf-pink-500 decoration-4 underline-offset-8' : '' }}">
                        Pesanan Saya
                    </a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin') }}" class="px-3.5 py-1.5 bg-hf-pink-500 text-white rounded-xl border-2 border-hf-purple-900 shadow-hard-sm hover:bg-hf-pink-400 text-xs font-black tracking-widest uppercase">
                            ⚡ Panel Admin
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Right: Auth Buttons / User Menu (Montserrat 700/800) -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- User Dropdown Menu -->
                    <div class="relative">
                        <button @click="userDropdownOpen = !userDropdownOpen" 
                                @click.away="userDropdownOpen = false"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-hf-magenta text-hf-yellow-400 font-sans font-extrabold text-sm uppercase tracking-wider border-3 border-hf-purple-900 rounded-2xl shadow-hard-sm hover:bg-hf-pink-500 transition-colors">
                            <span>👋 {{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 fill-current transition-transform" :class="{'rotate-180': userDropdownOpen}" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div x-show="userDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-56 bg-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl shadow-hard-lg py-2 z-50 overflow-hidden font-sans"
                             style="display: none;">
                            
                            <div class="px-4 py-2 border-b-2 border-hf-purple-700">
                                <p class="text-xs text-purple-200 font-semibold">Login sebagai:</p>
                                <p class="text-sm font-black text-hf-yellow-400 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ Route::has('orders.index') ? route('orders.index') : url('/orders') }}" class="block px-4 py-2.5 text-sm font-bold text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
                                🎟️ Pesanan & E-Tiket
                            </a>

                            @if (Auth::user()->isAdmin())
                                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin') }}" class="block px-4 py-2.5 text-sm font-bold text-hf-pink-400 hover:bg-hf-magenta">
                                    ⚙️ Dashboard Admin
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-bold text-rose-300 hover:bg-rose-950/60 hover:text-rose-200">
                                    🚪 Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="font-sans font-extrabold uppercase text-sm tracking-wider text-hf-yellow-400 hover:text-white px-3 py-2 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ url('/#tiket') }}" class="inline-flex items-center gap-1.5 font-sans font-extrabold text-sm uppercase px-5 py-2.5 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl shadow-hard-sm btn-press hover:bg-hf-yellow-500">
                        <span>Beli Tiket</span>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.6 11.025a.75.75 0 00-.914.593 8.462 8.462 0 001.077 5.86 8.441 8.441 0 004.832 3.82c.404.116.828.175 1.255.175a8.437 8.437 0 006.012-2.529 8.47 8.47 0 002.39-5.998c0-3.327-1.921-6.208-4.707-7.61a.75.75 0 00-.986.345 5.962 5.962 0 01-2.597 2.602zM12 10.5a3.75 3.75 0 00-3.75 3.75c0 .647.164 1.256.452 1.788a.75.75 0 001.298-.75A2.25 2.25 0 019.75 14.25a2.25 2.25 0 014.5 0c0 .388-.098.753-.272 1.072a.75.75 0 101.32.716c.45-.828.702-1.78.702-2.788A3.75 3.75 0 0012 10.5z" clip-rule="evenodd"/></svg>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="p-2.5 rounded-2xl bg-hf-magenta text-hf-yellow-400 border-3 border-hf-purple-900 shadow-hard-sm focus:outline-none"
                        aria-label="Buka Menu Navigasi">
                    <svg class="h-6 w-6 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="md:hidden bg-hf-purple-900 border-t-3 border-hf-purple-700 px-4 pt-3 pb-6 space-y-3 font-sans font-extrabold uppercase"
         style="display: none;">
        
        <a href="{{ url('/') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
            🎸 Beranda
        </a>
        <a href="{{ route('events.index') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
            🗓️ Daftar Event & Arsip
        </a>
        <a href="{{ url('/#lineup') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
            👥 Lineup Band
        </a>
        <a href="{{ url('/#tiket') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
            🎟️ Harga & Beli Tiket
        </a>

        @auth
            <a href="{{ Route::has('orders.index') ? route('orders.index') : url('/orders') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-white hover:bg-hf-magenta hover:text-hf-yellow-400">
                📦 Pesanan Saya
            </a>
            @if (Auth::user()->isAdmin())
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-xl text-base text-hf-pink-400 hover:bg-hf-magenta">
                    ⚡ Panel Admin
                </a>
            @endif
            <div class="pt-3 border-t-2 border-hf-purple-700 normal-case font-normal">
                <p class="text-xs text-purple-200 px-3">Halo, <strong>{{ Auth::user()->name }}</strong></p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-base font-bold text-rose-300 hover:bg-rose-950/50">
                        🚪 Keluar (Logout)
                    </button>
                </form>
            </div>
        @else
            <div class="pt-3 border-t-2 border-hf-purple-700 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-2xl bg-hf-magenta text-hf-yellow-400 border-2 border-hf-purple-900 shadow-hard-sm text-sm font-black">
                    Masuk Akun
                </a>
                <a href="{{ route('register') }}" class="w-full text-center py-2.5 rounded-2xl bg-hf-yellow-400 text-hf-purple-900 border-2 border-hf-purple-900 shadow-hard-sm text-sm font-black">
                    Daftar Akun Baru
                </a>
            </div>
        @endauth
    </div>
</nav>
