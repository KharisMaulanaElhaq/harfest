<x-admin-layout>
    <x-slot:title>Kelola Event - Harfest Admin</x-slot:title>
    <x-slot:header>Kelola Edisi Event Konser</x-slot:header>

    <div class="space-y-6">

        <!-- Top Header & Actions -->
        <div class="bg-white p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                    Daftar Seri & Edisi Harfest
                </h2>
                <p class="text-xs text-slate-500 font-sans mt-0.5">
                    Kelola siklus hidup tiap volume event, jadwal penjualan, duplikasi edisi, dan status aktif.
                </p>
            </div>

            <div>
                <a href="{{ route('admin.events.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs btn-press cursor-pointer">
                    <span>➕</span>
                    <span>Tambah Edisi Baru</span>
                </a>
            </div>
        </div>

        <!-- Event Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse ($events as $ev)
                @php
                    $isWorkingEvent = isset($currentAdminEvent) && $currentAdminEvent->id === $ev->id;
                @endphp
                <div class="bg-white rounded-3xl border-3 {{ $isWorkingEvent ? 'border-hf-purple-900 ring-4 ring-hf-yellow-400/50' : 'border-slate-300' }} shadow-hard overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Card Header Banner -->
                        <div class="p-5 {{ $isWorkingEvent ? 'bg-hf-purple-900 text-white' : 'bg-slate-100 text-slate-800' }} border-b-2 border-slate-200 flex justify-between items-start gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-hf-yellow-400 text-hf-purple-900 font-display text-[11px] font-black uppercase rounded-md tracking-wider">
                                        VOL. {{ $ev->edition }}
                                    </span>
                                    <x-status-badge :status="$ev->status" />
                                    @if ($isWorkingEvent)
                                        <span class="px-2 py-0.5 bg-emerald-400 text-emerald-950 font-sans text-[10px] font-black uppercase rounded-md">
                                            ⚡ Event Kerja Admin
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-display text-xl uppercase leading-tight {{ $isWorkingEvent ? 'text-white' : 'text-slate-900' }}">
                                    {{ $ev->name }}
                                </h3>
                                @if ($ev->tagline)
                                    <p class="text-xs font-sans italic mt-0.5 {{ $isWorkingEvent ? 'text-purple-200' : 'text-slate-500' }}">
                                        "{{ $ev->tagline }}"
                                    </p>
                                @endif
                            </div>

                            <a href="{{ route('events.show', $ev->slug) }}" target="_blank"
                               class="shrink-0 text-[11px] font-sans font-bold {{ $isWorkingEvent ? 'text-hf-yellow-400 hover:text-white' : 'text-purple-700 hover:underline' }}">
                                Preview ↗
                            </a>
                        </div>

                        <!-- Card Body Details -->
                        <div class="p-5 space-y-4 text-xs font-sans">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700 bg-slate-50 p-3 rounded-2xl border border-slate-200">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Tanggal Konser:</span>
                                    <strong>{{ $ev->event_date ? format_wib($ev->event_date) : 'Belum ditentukan' }}</strong>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Lokasi:</span>
                                    <span class="truncate block">{{ $ev->venue }}</span>
                                </div>
                                <div class="sm:col-span-2 pt-1 border-t border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Jadwal Tiket:</span>
                                    @if ($ev->sales_open_at || $ev->sales_close_at)
                                        <span class="text-[11px] font-medium text-slate-600">
                                            {{ $ev->sales_open_at ? format_wib($ev->sales_open_at) : 'Mulai sekarang' }} s/d 
                                            {{ $ev->sales_close_at ? format_wib($ev->sales_close_at) : 'Selesai konser' }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-500">Mengikuti status publikasi ({{ $ev->status->label() }})</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Visual Assets Preview -->
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-12 bg-hf-purple-900 rounded-xl flex items-center justify-center p-1 shrink-0 overflow-hidden border">
                                    <img src="{{ $ev->logoUrl() }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <p class="font-bold text-[11px] text-slate-800">Slug: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-purple-700">/events/{{ $ev->slug }}</code></p>
                                    <div class="flex gap-3 text-slate-500 text-[11px] mt-1">
                                        <span>🎸 {{ $ev->bands_count }} Band</span>
                                        <span>🎟️ {{ $ev->ticket_types_count }} Tiket</span>
                                        <span>📦 {{ $ev->orders_count }} Pesanan</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Status Update -->
                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                                <span class="text-xs font-bold text-slate-700 shrink-0">Ubah Status:</span>
                                <form action="{{ route('admin.events.update-status', $ev->id) }}" method="POST" class="flex items-center gap-2 flex-grow justify-end">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" 
                                            class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-slate-300 font-sans font-bold bg-white focus:outline-none focus:ring-2 focus:ring-hf-purple-900">
                                        @foreach (\App\Enums\EventStatus::cases() as $st)
                                            <option value="{{ $st->value }}" {{ $ev->status === $st ? 'selected' : '' }}>
                                                {{ $st->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="p-4 bg-slate-50 border-t-2 border-slate-200 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <!-- Edit Button -->
                            <a href="{{ route('admin.events.edit', $ev->id) }}" 
                               class="px-3 py-1.5 bg-hf-yellow-400 text-hf-purple-900 font-display text-xs uppercase rounded-xl border border-hf-purple-900 hover:bg-hf-yellow-500">
                                ✏️ Edit
                            </a>

                            <!-- Duplicate Button -->
                            <form action="{{ route('admin.events.duplicate', $ev->id) }}" method="POST"
                                  onsubmit="return confirm('Duplikasi event {{ $ev->name }} beserta jenis tiketnya menjadi edisi baru (draft)?');">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1.5 bg-purple-100 text-hf-purple-900 hover:bg-purple-200 font-display text-xs uppercase rounded-xl border border-purple-300">
                                    📋 Duplikat
                                </button>
                            </form>

                            <!-- Switch Button -->
                            @if (!$isWorkingEvent)
                                <form action="{{ route('admin.events.switch', $ev->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-1.5 bg-emerald-100 text-emerald-900 hover:bg-emerald-200 font-display text-xs uppercase rounded-xl border border-emerald-300">
                                        ⚡ Buka Kerja
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Delete Button if no orders -->
                        @if ($ev->orders_count === 0)
                            <form action="{{ route('admin.events.destroy', $ev->id) }}" method="POST" onsubmit="return confirm('Hapus permanen event ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold font-sans">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-10 bg-white rounded-3xl border-3 border-dashed border-slate-300 text-center text-slate-400">
                    Belum ada data event konser. Silakan klik tombol "Tambah Edisi Baru".
                </div>
            @endforelse
        </div>

    </div>
</x-admin-layout>
