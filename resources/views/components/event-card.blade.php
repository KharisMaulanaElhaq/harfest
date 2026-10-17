@props([
    'event',
])

@php
    $isFinished = $event->status === \App\Enums\EventStatus::Finished;
    $isOnSale = $event->status === \App\Enums\EventStatus::OnSale;
    $isUpcoming = $event->status === \App\Enums\EventStatus::Upcoming;
@endphp

<div class="flex flex-col h-full rounded-3xl border-4 border-hf-purple-900 bg-white text-hf-purple-900 shadow-hard-lg overflow-hidden transition-all duration-300 hover:-translate-y-1.5 hover:shadow-[8px_8px_0_#4D0570] group">
    <!-- Header with edition & status badge -->
    <div class="p-4 sm:p-5 {{ $isOnSale ? 'bg-hf-magenta text-white' : ($isUpcoming ? 'bg-hf-purple-900 text-white' : 'bg-gray-800 text-gray-200') }} border-b-4 border-hf-purple-900 flex justify-between items-center">
        <div class="inline-flex items-center gap-2">
            <span class="px-3 py-1 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase tracking-wider rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                VOL {{ $event->edition }}
            </span>
            @if ($event->tagline)
                <span class="hidden sm:inline font-sans text-xs font-bold truncate max-w-xs opacity-90">
                    {{ $event->tagline }}
                </span>
            @endif
        </div>

        <x-status-badge :status="$event->status" />
    </div>

    <!-- Body -->
    <div class="p-5 sm:p-6 flex-grow flex flex-col justify-between">
        <div>
            <!-- Event Name -->
            <h3 class="font-sans font-black text-2xl sm:text-3xl text-hf-purple-900 tracking-tight uppercase leading-tight mb-2 group-hover:text-hf-pink-500 transition-colors">
                <a href="{{ route('events.show', $event->slug) }}">
                    {{ $event->name }}
                </a>
            </h3>

            <!-- Date & Venue -->
            <div class="space-y-1.5 my-3 text-xs sm:text-sm font-sans font-bold text-gray-700">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-hf-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span>{{ $event->event_date->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-hf-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span class="truncate">{{ $event->venue }}</span>
                </div>
            </div>

            <!-- Description -->
            <p class="text-xs sm:text-sm text-gray-600 font-sans leading-relaxed line-clamp-2 my-3">
                {{ $event->description }}
            </p>

            <!-- Headliner snippet if available -->
            @php
                $headliner = $event->headliners()->first();
            @endphp
            @if ($headliner)
                <div class="p-2.5 bg-yellow-50 border-2 border-dashed border-yellow-300 rounded-2xl flex items-center gap-2 my-3">
                    <span class="text-base">⭐</span>
                    <div class="text-xs font-sans">
                        <span class="text-gray-500 font-bold">Headliner:</span>
                        <strong class="text-hf-purple-900 font-black ml-1">{{ $headliner->name }}</strong>
                    </div>
                </div>
            @endif
        </div>

        <!-- Card Footer Action -->
        <div class="pt-4 border-t-2 border-gray-100 flex items-center justify-between gap-3">
            <span class="text-xs font-sans font-bold text-gray-500">
                {{ $event->bands_count ?? $event->bands()->count() }} Band Lineup
            </span>

            @if ($isOnSale && $event->isSalesOpen())
                <a href="{{ route('events.show', $event->slug) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-hf-yellow-400 text-hf-purple-900 font-sans font-extrabold text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-sm btn-press hover:bg-hf-yellow-500">
                    <span>Beli Tiket</span>
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </a>
            @else
                <a href="{{ route('events.show', $event->slug) }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-hf-purple-900 text-white font-sans font-extrabold text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-sm btn-press hover:bg-hf-pink-500">
                    <span>Lihat Detail</span>
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </a>
            @endif
        </div>
    </div>
</div>
