<x-admin-layout>
    <x-slot:title>Lineup Band - Harfest Admin</x-slot:title>
    <x-slot:header>Kelola Lineup Bintang Tamu</x-slot:header>

    <div class="space-y-6">

        <div class="bg-white p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                    Daftar Lineup Band Konser
                </h2>
                <p class="text-xs text-slate-500 font-sans mt-0.5">
                    Atur nama band bintang tamu, headliner utama, logo tipografi, foto artis, dan urutan tampil.
                    @if ($currentEvent)
                        <span class="font-bold text-hf-purple-900">Menampilkan untuk: {{ $currentEvent->name }}</span>
                    @endif
                </p>
            </div>

            <div>
                <a href="{{ route('admin.bands.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs btn-press">
                    <span>➕</span>
                    <span>Tambah Band Lineup</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($bands as $band)
                <div class="bg-white rounded-3xl border-3 {{ $band->is_headliner ? 'border-hf-yellow-400 ring-4 ring-hf-yellow-400/30' : 'border-hf-purple-900' }} shadow-hard overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Photo container -->
                        <div class="relative aspect-video bg-black overflow-hidden border-b-2 border-hf-purple-900">
                            @if ($band->photo)
                                <img src="{{ asset('storage/' . $band->photo) }}" alt="{{ $band->name }}" class="w-full h-full object-cover band-grayscale-filter">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-purple-950 text-purple-300 font-display text-lg">
                                    {{ $band->name }}
                                </div>
                            @endif
                            <div class="absolute top-2 left-2 flex gap-1 items-center">
                                <span class="px-2 py-0.5 bg-hf-yellow-400 text-hf-purple-900 font-display text-[10px] rounded-lg border border-hf-purple-900 font-bold">
                                    #{{ $band->sort_order }}
                                </span>
                                @if ($band->is_headliner)
                                    <span class="px-2 py-0.5 bg-hf-pink-500 text-white font-display text-[10px] rounded-lg border border-hf-purple-900 font-black tracking-wider">
                                        ⭐ HEADLINER
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <h3 class="font-display text-lg text-hf-purple-900 uppercase tracking-wide">
                                    {{ $band->name }}
                                </h3>
                                @if ($band->logo)
                                    <div class="h-6 w-12 bg-hf-purple-900 rounded p-0.5 flex items-center justify-center shrink-0">
                                        <img src="{{ $band->logoUrl() }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                    </div>
                                @endif
                            </div>
                            
                            @if ($band->instagram_url)
                                <a href="https://instagram.com/{{ ltrim(str_replace('https://instagram.com/', '', $band->instagram_url), '@') }}" 
                                   target="_blank" class="text-[11px] text-hf-pink-500 font-bold hover:underline block">
                                    📸 {{ $band->instagram_url }}
                                </a>
                            @endif

                            <p class="text-xs text-slate-600 font-sans line-clamp-3">
                                {{ $band->description }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-3 bg-slate-50 border-t-2 border-slate-200 flex items-center justify-between">
                        <a href="{{ route('admin.bands.edit', $band->id) }}" 
                           class="px-3 py-1.5 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border border-hf-purple-900">
                            ✏️ Edit
                        </a>

                        <form action="{{ route('admin.bands.destroy', $band->id) }}" method="POST" onsubmit="return confirm('Hapus band {{ $band->name }} dari lineup?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold font-sans p-1">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-10 bg-white rounded-3xl border-3 border-dashed border-slate-300 text-center text-slate-400">
                    Belum ada band di lineup konser untuk event ini. Silakan klik tombol "Tambah Band Lineup".
                </div>
            @endforelse
        </div>

    </div>
</x-admin-layout>
