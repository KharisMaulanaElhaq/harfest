<x-admin-layout>
    <x-slot:title>Kategori Tiket - Harfest Admin</x-slot:title>
    <x-slot:header>Kelola Kategori & Harga Tiket</x-slot:header>

    <div class="space-y-6">

        <div class="bg-white p-5 rounded-3xl border-3 border-hf-purple-900 shadow-hard-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-xl text-hf-purple-900 uppercase">
                    Daftar Kategori Tiket
                </h2>
                <p class="text-xs text-slate-500 font-sans mt-0.5">
                    Atur harga, kuota tiket, jumlah orang per paket, dan syarat kartu mahasiswa civitas.
                </p>
            </div>

            <div>
                <a href="{{ route('admin.ticket-types.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs btn-press">
                    <span>➕</span>
                    <span>Tambah Kategori Tiket</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl border-3 border-hf-purple-900 shadow-hard overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left font-sans text-xs">
                    <thead class="bg-hf-purple-900 text-white font-display uppercase tracking-wider border-b-2 border-hf-purple-950">
                        <tr>
                            <th class="p-3.5">Nama Kategori</th>
                            <th class="p-3.5">Harga</th>
                            <th class="p-3.5 text-center">Orang/Tiket</th>
                            <th class="p-3.5 text-center">Kuota Total</th>
                            <th class="p-3.5 text-center">Terjual / Dipesan</th>
                            <th class="p-3.5 text-center">Sisa Kuota</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($ticketTypes as $type)
                            @php
                                $committed = $type->committedQuota();
                                $remaining = $type->remaining_quota;
                            @endphp
                            <tr class="hover:bg-purple-50/40 transition-colors">
                                <td class="p-3.5">
                                    <strong class="font-display text-sm text-hf-purple-900 uppercase block">
                                        {{ $type->name }}
                                    </strong>
                                    <span class="text-[11px] text-slate-500 line-clamp-1 max-w-xs">{{ $type->description }}</span>
                                    @if ($type->requires_student_id)
                                        <span class="inline-block mt-1 px-1.5 py-0.2 bg-pink-100 text-pink-700 font-bold rounded text-[10px]">
                                            🎓 Wajib KTM Civitas
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3.5 font-display text-sm font-bold text-hf-purple-900">
                                    Rp{{ number_format($type->price, 0, ',', '.') }}
                                </td>

                                <td class="p-3.5 text-center font-bold text-slate-700">
                                    {{ $type->people_per_ticket }} orang
                                </td>

                                <td class="p-3.5 text-center font-display text-sm font-bold text-slate-800">
                                    {{ $type->quota }}
                                </td>

                                <td class="p-3.5 text-center font-bold text-hf-pink-600">
                                    {{ $committed }} paket
                                </td>

                                <td class="p-3.5 text-center font-display text-sm font-bold {{ $remaining <= 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $remaining }}
                                </td>

                                <td class="p-3.5 text-center">
                                    @if ($type->is_active)
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">
                                            AKTIF
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-slate-200 text-slate-600 rounded-full font-bold text-[10px]">
                                            NON-AKTIF
                                        </span>
                                    @endif
                                </td>

                                <td class="p-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.ticket-types.edit', $type->id) }}" 
                                           class="px-3 py-1 bg-hf-yellow-400 hover:bg-hf-yellow-500 text-hf-purple-900 font-display text-xs uppercase rounded-lg border border-hf-purple-900">
                                            Edit
                                        </a>

                                        @if ($type->orders()->count() === 0)
                                            <form action="{{ route('admin.ticket-types.destroy', $type->id) }}" method="POST" onsubmit="return confirm('Hapus kategori tiket ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs p-1">
                                                    ✕
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400 italic">
                                    Belum ada kategori tiket.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
