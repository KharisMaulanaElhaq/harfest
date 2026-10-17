<x-admin-layout>
    <x-slot:title>Tambah Band Lineup - Harfest Admin</x-slot:title>
    <x-slot:header>Tambah Band Bintang Tamu</x-slot:header>

    <div class="max-w-2xl space-y-6">

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.bands.index') }}" 
               class="inline-flex items-center gap-2 font-display text-xs text-hf-purple-900 bg-white hover:bg-slate-50 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                <span>← Kembali ke Daftar Band</span>
            </a>
        </div>

        <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-6 sm:p-8">
            <form action="{{ route('admin.bands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Event Select -->
                <div>
                    <label for="event_id" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Pilih Event Konser <span class="text-rose-500">*</span>
                    </label>
                    <select id="event_id" name="event_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400 bg-white">
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}" {{ (old('event_id', $currentEvent?->id) == $ev->id) ? 'selected' : '' }}>
                                {{ $ev->name }} (Vol. {{ $ev->edition }})
                            </option>
                        @endforeach
                    </select>
                    @error('event_id') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Headliner Checkbox -->
                <div class="p-4 bg-hf-yellow-400/20 rounded-2xl border-2 border-hf-yellow-400 flex items-center gap-3">
                    <input type="checkbox" id="is_headliner" name="is_headliner" value="1" {{ old('is_headliner') ? 'checked' : '' }}
                           class="w-5 h-5 rounded text-hf-purple-900 focus:ring-hf-yellow-400 border-2 border-hf-purple-900">
                    <label for="is_headliner" class="text-xs font-sans text-hf-purple-900 font-black cursor-pointer">
                        ⭐ Tandai sebagai Bintang Tamu Utama (Headliner)
                    </label>
                </div>
                <p class="text-[11px] text-slate-500 -mt-2 ml-1">
                    Headliner akan ditampilkan menonjol di kartu cutout beroutline kuning/pink dengan maskot globe & player.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="name" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Nama Band / Artis <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Enau, Artura" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @error('name') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Urutan Tampil <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $nextOrder) }}" min="0" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @error('sort_order') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Instagram URL -->
                <div>
                    <label for="instagram_url" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Akun Instagram (Opsional)
                    </label>
                    <div class="flex rounded-xl shadow-xs">
                        <span class="inline-flex items-center px-3 rounded-l-xl border-2 border-r-0 border-hf-purple-900 bg-slate-100 text-slate-500 text-xs font-mono">
                            https://instagram.com/
                        </span>
                        <input type="text" id="instagram_url" name="instagram_url" value="{{ old('instagram_url') }}"
                               placeholder="nama_akun"
                               class="w-full px-3.5 py-2.5 rounded-r-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                    </div>
                    @error('instagram_url') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Deskripsi / Genre Singkat <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                              placeholder="Deskripsi singkat band dan genre musik..."
                              class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Photo Upload -->
                    <div>
                        <label for="photo" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Foto Band / Artis (Maks 2 MB)
                        </label>
                        <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-2 file:border-hf-purple-900 file:text-xs file:font-display file:bg-hf-yellow-400 file:text-hf-purple-900 hover:file:bg-hf-yellow-500">
                        @error('photo') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Logo Tipografi Band (PNG/SVG)
                        </label>
                        <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.svg"
                               class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-2 file:border-hf-purple-900 file:text-xs file:font-display file:bg-hf-yellow-400 file:text-hf-purple-900 hover:file:bg-hf-yellow-500">
                        @error('logo') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press cursor-pointer">
                        ➕ Simpan Band
                    </button>
                    <a href="{{ route('admin.bands.index') }}" class="px-4 py-3 text-xs font-display text-slate-500 hover:text-slate-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-admin-layout>
