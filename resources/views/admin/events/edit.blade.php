<x-admin-layout>
    <x-slot:title>Edit Event {{ $event->name }} - Harfest Admin</x-slot:title>
    <x-slot:header>Edit Informasi Event</x-slot:header>

    <div class="max-w-4xl space-y-6">

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.events.index') }}" 
               class="inline-flex items-center gap-2 font-display text-xs text-hf-purple-900 bg-white hover:bg-slate-50 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                <span>← Kembali ke Daftar Event</span>
            </a>

            <div class="flex items-center gap-2">
                <form action="{{ route('admin.events.switch', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs rounded-xl border-2 border-hf-purple-900 shadow-hard-xs hover:bg-hf-yellow-500">
                        ⚡ Jadikan Event Kerja
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-6 sm:p-8">
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="font-display text-base text-hf-purple-900 uppercase border-b-2 border-slate-100 pb-2 flex items-center gap-2">
                        <span>🎪</span> Identitas Event & Edisi
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Nama Event <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                            @error('name') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="edition" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Nomor Edisi <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="edition" name="edition" value="{{ old('edition', $event->edition) }}" min="1" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                            @error('edition') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="slug" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Slug URL <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl shadow-xs">
                                <span class="inline-flex items-center px-3 rounded-l-xl border-2 border-r-0 border-hf-purple-900 bg-slate-100 text-slate-500 text-xs font-mono">
                                    /events/
                                </span>
                                <input type="text" id="slug" name="slug" value="{{ old('slug', $event->slug) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-r-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 font-mono">
                            </div>
                            @error('slug') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Status Publikasi <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 bg-white">
                                @foreach (\App\Enums\EventStatus::cases() as $st)
                                    <option value="{{ $st->value }}" {{ old('status', $event->status->value) === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }} ({{ $st->value }})
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="tagline" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Tagline / Slogan Event
                        </label>
                        <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $event->tagline) }}"
                               placeholder="Contoh: MARI CIPTAKAN GEMURUH BERSAMA!"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @error('tagline') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Venue & Dates -->
                <div class="space-y-4 pt-4 border-t-2 border-slate-100">
                    <h3 class="font-display text-base text-hf-purple-900 uppercase border-b-2 border-slate-100 pb-2 flex items-center gap-2">
                        <span>📍</span> Waktu & Tempat
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="venue" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Lokasi / Venue <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="venue" name="venue" value="{{ old('venue', $event->venue) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                            @error('venue') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="event_date" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="datetime-local" id="event_date" name="event_date" 
                                   value="{{ old('event_date', $event->event_date?->format('Y-m-d\TH:i')) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                            @error('event_date') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Sales Schedule -->
                    <div class="p-4 bg-purple-50/60 rounded-2xl border-2 border-purple-200 space-y-3">
                        <span class="font-display text-xs uppercase tracking-wider text-hf-purple-950 block">
                            ⏰ Jadwal Periode Penjualan Tiket (Opsional)
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="sales_open_at" class="block text-xs font-sans text-slate-700 font-bold mb-1">
                                    Tiket Mulai Dijual:
                                </label>
                                <input type="datetime-local" id="sales_open_at" name="sales_open_at" 
                                       value="{{ old('sales_open_at', $event->sales_open_at?->format('Y-m-d\TH:i')) }}"
                                       class="w-full px-3.5 py-2 bg-white rounded-xl border border-purple-300 font-sans text-xs focus:ring-2 focus:ring-hf-yellow-400">
                                @error('sales_open_at') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sales_close_at" class="block text-xs font-sans text-slate-700 font-bold mb-1">
                                    Tiket Ditutup Pada:
                                </label>
                                <input type="datetime-local" id="sales_close_at" name="sales_close_at" 
                                       value="{{ old('sales_close_at', $event->sales_close_at?->format('Y-m-d\TH:i')) }}"
                                       class="w-full px-3.5 py-2 bg-white rounded-xl border border-purple-300 font-sans text-xs focus:ring-2 focus:ring-hf-yellow-400">
                                @error('sales_close_at') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-4 border-t-2 border-slate-100">
                    <label for="description" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Deskripsi Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">{{ old('description', $event->description) }}</textarea>
                    @error('description') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Media Uploads: Logo, Banner, Poster -->
                <div class="space-y-4 pt-4 border-t-2 border-slate-100">
                    <h3 class="font-display text-base text-hf-purple-900 uppercase border-b-2 border-slate-100 pb-2 flex items-center gap-2">
                        <span>🎨</span> Aset Visual & Branding
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Logo -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
                            <div>
                                <label for="logo" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                    Logo Edisi
                                </label>
                                @if ($event->logo)
                                    <div class="mb-2 p-2 bg-hf-purple-900 rounded-xl flex items-center justify-center">
                                        <img src="{{ $event->logoUrl() }}" alt="Logo" class="h-10 w-auto object-contain">
                                    </div>
                                @endif
                                <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.svg"
                                       class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-hf-purple-900 file:text-[11px] file:font-display file:bg-hf-yellow-400 file:text-hf-purple-900">
                            </div>
                            @error('logo') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Banner -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
                            <div>
                                <label for="banner" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                    Banner Landscape
                                </label>
                                @if ($event->banner)
                                    <div class="mb-2 p-1 bg-white rounded-xl border">
                                        <img src="{{ $event->bannerUrl() }}" alt="Banner" class="h-14 w-full object-cover rounded-lg">
                                    </div>
                                @endif
                                <input type="file" id="banner" name="banner" accept=".jpg,.jpeg,.png"
                                       class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-hf-purple-900 file:text-[11px] file:font-display file:bg-hf-yellow-400 file:text-hf-purple-900">
                            </div>
                            @error('banner') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Poster -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
                            <div>
                                <label for="poster" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                                    Poster Portrait
                                </label>
                                @if ($event->poster)
                                    <div class="mb-2 p-1 bg-white rounded-xl border">
                                        <img src="{{ asset('storage/' . $event->poster) }}" alt="Poster" class="h-16 w-auto object-cover rounded">
                                    </div>
                                @endif
                                <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png"
                                       class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-hf-purple-900 file:text-[11px] file:font-display file:bg-hf-yellow-400 file:text-hf-purple-900">
                            </div>
                            @error('poster') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t-2 border-slate-100 flex items-center gap-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press cursor-pointer">
                        💾 Simpan Perubahan Event
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="px-4 py-3 text-xs font-display text-slate-500 hover:text-slate-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-admin-layout>
