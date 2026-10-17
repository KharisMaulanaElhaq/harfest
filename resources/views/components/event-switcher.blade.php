@props([
    'currentEvent' => null,
    'events' => collect(),
])

@php
    $events = $events instanceof \Illuminate\Support\Collection ? $events : collect($events);
@endphp

<div x-data="{ switcherOpen: false }" class="relative w-full">
    <div class="px-3 py-2 text-[10px] font-sans font-black text-hf-pink-400 uppercase tracking-widest flex items-center justify-between">
        <span>Event Kerja</span>
        @if ($currentEvent)
            <span class="text-[9px] bg-hf-yellow-400 text-hf-purple-900 px-1.5 py-0.5 rounded font-black">
                VOL {{ $currentEvent->edition }}
            </span>
        @endif
    </div>

    <!-- Switcher Trigger Button -->
    <button @click="switcherOpen = !switcherOpen" 
            @click.away="switcherOpen = false"
            type="button"
            class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 bg-hf-purple-950/80 hover:bg-hf-purple-950 text-white rounded-2xl border-2 border-hf-purple-700/60 shadow-sm transition-all text-left">
        <div class="min-w-0 flex-grow">
            <p class="font-sans font-black text-xs text-hf-yellow-400 truncate">
                {{ $currentEvent ? $currentEvent->name : 'Pilih Event' }}
            </p>
            <p class="text-[10px] text-purple-300 font-semibold truncate">
                {{ $currentEvent ? $currentEvent->status->label() . ' • ' . $currentEvent->event_date->translatedFormat('d M Y') : 'Belum ada event' }}
            </p>
        </div>

        <svg class="w-4 h-4 text-hf-pink-400 shrink-0 transition-transform" :class="{'rotate-180': switcherOpen}" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>

    <!-- Switcher Dropdown Menu -->
    <div x-show="switcherOpen" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute left-0 right-0 mt-2 bg-hf-purple-900 border-3 border-hf-purple-950 rounded-2xl shadow-hard-lg py-1.5 z-50 overflow-hidden font-sans"
         style="display: none;">
        
        <div class="px-3 py-1.5 border-b border-hf-purple-800 text-[10px] font-black uppercase tracking-wider text-purple-300">
            Ganti Event Kerja Admin
        </div>

        <div class="max-h-60 overflow-y-auto divide-y divide-hf-purple-800/40">
            @forelse ($events as $item)
                @php
                    $isSelected = $currentEvent && $currentEvent->id === $item->id;
                @endphp
                <form method="POST" action="{{ route('admin.events.switch', $item->id) }}" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left px-3.5 py-2 hover:bg-hf-magenta flex items-center justify-between gap-2 transition-colors {{ $isSelected ? 'bg-hf-purple-950/70' : '' }}">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-xs {{ $isSelected ? 'text-hf-yellow-400' : 'text-white' }} truncate">
                                    {{ $item->name }}
                                </span>
                                @if ($isSelected)
                                    <span class="text-xs text-hf-yellow-400">✓</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-purple-300 block">
                                Vol {{ $item->edition }} • {{ $item->status->label() }}
                            </span>
                        </div>

                        <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase shrink-0 {{ $item->status === \App\Enums\EventStatus::OnSale ? 'bg-hf-yellow-400 text-hf-purple-900' : ($item->status === \App\Enums\EventStatus::Finished ? 'bg-purple-950 text-purple-300' : 'bg-hf-blue-400 text-hf-purple-900') }}">
                            {{ $item->status->value }}
                        </span>
                    </button>
                </form>
            @empty
                <div class="px-3 py-2 text-xs text-purple-300 text-center">
                    Tidak ada event lain.
                </div>
            @endforelse
        </div>

        <div class="pt-1.5 border-t border-hf-purple-800 px-2">
            <a href="{{ route('admin.events.create') }}" 
               class="block text-center py-1.5 px-3 rounded-xl bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs hover:bg-hf-yellow-500 uppercase tracking-wider">
                + Tambah Event Baru
            </a>
        </div>
    </div>
</div>
