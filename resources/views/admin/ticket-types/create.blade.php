<x-admin-layout>
    <x-slot:title>Tambah Kategori Tiket - Harfest Admin</x-slot:title>
    <x-slot:header>Tambah Kategori Tiket Baru</x-slot:header>

    <div class="max-w-3xl space-y-6">

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.ticket-types.index') }}" 
               class="inline-flex items-center gap-2 font-display text-xs text-hf-purple-900 bg-white hover:bg-slate-50 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                <span>← Kembali ke Daftar Tiket</span>
            </a>
        </div>

        <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard p-6 sm:p-8">
            <form action="{{ route('admin.ticket-types.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Event Select -->
                <div>
                    <label for="event_id" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Pilih Event Konser <span class="text-rose-500">*</span>
                    </label>
                    <select id="event_id" name="event_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->name }} ({{ $ev->venue }})</option>
                        @endforeach
                    </select>
                    @error('event_id') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Nama Kategori Tiket <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Tiket Umum VIP 1 Orang" required
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                    @error('name') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="price" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Harga Tiket (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price', 40000) }}" min="0" step="1000" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @error('price') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="quota" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Kuota Total <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="quota" name="quota" value="{{ old('quota', 50) }}" min="1" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        @error('quota') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="people_per_ticket" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                            Orang Per Tiket <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="people_per_ticket" name="people_per_ticket" value="{{ old('people_per_ticket', 1) }}" min="1" max="10" required
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">
                        <span class="text-[10px] text-slate-400">Isi 3 untuk paket bundling 3 orang</span>
                        @error('people_per_ticket') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block font-display text-xs uppercase tracking-wider text-hf-purple-900 mb-1">
                        Deskripsi / Fasilitas Tiket <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                              placeholder="Fasilitas dan rincian yang didapatkan oleh pembeli kategori tiket ini..."
                              class="w-full px-3.5 py-2.5 rounded-xl border-2 border-hf-purple-900 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-hf-yellow-400">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Checkboxes -->
                <div class="space-y-3 p-4 bg-purple-50 rounded-2xl border border-purple-200">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="requires_student_id" name="requires_student_id" value="1" {{ old('requires_student_id') ? 'checked' : '' }}
                               class="w-5 h-5 rounded text-hf-purple-900 focus:ring-hf-yellow-400">
                        <label for="requires_student_id" class="text-xs font-sans text-purple-950 font-bold cursor-pointer">
                            🎓 Khusus Civitas Akademika (Wajib input NIM dan unggah foto KTM saat pesan)
                        </label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="w-5 h-5 rounded text-hf-purple-900 focus:ring-hf-yellow-400">
                        <label for="is_active" class="text-xs font-sans text-purple-950 font-bold cursor-pointer">
                            Aktifkan kategori ini agar bisa dipesan di Landing Page
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-sm uppercase rounded-2xl border-3 border-hf-purple-900 shadow-hard btn-press cursor-pointer">
                        ➕ Simpan Kategori Tiket
                    </button>
                    <a href="{{ route('admin.ticket-types.index') }}" class="px-4 py-3 text-xs font-display text-slate-500 hover:text-slate-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</x-admin-layout>
