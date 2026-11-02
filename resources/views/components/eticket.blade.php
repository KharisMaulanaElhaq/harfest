@props([
    'ticket',
    'order',
    'event' => null,
    'index' => 0,
    'total' => 1,
])

@php
    $event = $event ?? $order->event ?? $order->ticketType?->event;
    $qrService = app(\App\Services\QrService::class);
    $qrDataUri = $qrService->dataUri($ticket->ticket_code, 'png', 180);
@endphp

<div class="ticket-card relative bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg overflow-hidden flex flex-col md:flex-row transition-all hover:-translate-y-1">
    
    <!-- LEFT SECTION: Main Concert Stub (~70%) -->
    <div class="flex-1 p-6 sm:p-7 flex flex-col justify-between relative bg-gradient-to-br from-white via-white to-purple-50/60">
        
        <!-- Header Strip -->
        <div>
            <div class="flex items-center justify-between gap-2 border-b-3 border-hf-purple-900/20 pb-3 mb-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/brand/harfest-logo.png') }}" alt="Harfest" class="h-8 w-auto">
                    <div>
                        <span class="font-sans font-black text-lg text-hf-purple-900 tracking-wider block leading-none">
                            {{ $event?->name ?? 'HARFEST 2026' }}
                        </span>
                        <span class="font-sans text-[10px] text-hf-pink-500 font-bold uppercase tracking-wider">
                            {{ $event?->tagline ?? 'Universitas Harkat Negeri' }}
                        </span>
                    </div>
                </div>

                <div class="px-3 py-1 bg-hf-yellow-400 text-hf-purple-900 font-sans font-black text-xs uppercase rounded-xl border-2 border-hf-purple-900 shadow-hard-xs">
                    {{ $order->ticketType->name }}
                </div>
            </div>

            <!-- Ticket Holder Name & Pass Number -->
            <div class="my-2">
                <span class="text-[11px] font-sans uppercase font-bold text-gray-500 tracking-wider block">
                    Nama Pemegang Tiket:
                </span>
                <h3 class="font-sans font-black text-2xl sm:text-3xl text-hf-purple-900 tracking-wide mt-0.5">
                    {{ $ticket->holder_name }}
                </h3>
                <span class="text-xs font-sans text-gray-500 block mt-0.5">
                    Tiket #{{ $index + 1 }} dari {{ $total }} tiket pesanan (Ref: {{ $order->booking_code }})
                </span>
            </div>
        </div>

        <!-- Concert Event Details -->
        <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-sans">
            <div>
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Tanggal & Waktu:</span>
                <strong class="text-slate-900 block font-bold">
                    {{ $event?->event_date ? format_wib($event->event_date) : 'Sabtu, 28 November 2026, 14:00 WIB' }}
                </strong>
                <span class="text-gray-600">Open Gate: 14:00 WIB | Show: 15:00 WIB</span>
            </div>

            <div>
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Lokasi Acara:</span>
                <strong class="text-slate-900 block font-bold">
                    {{ $event?->venue ?? 'Lapangan Utama Kampus' }}
                </strong>
                <span class="text-gray-600">Universitas Harkat Negeri</span>
            </div>
        </div>

        <!-- Ticket Rules Small Footer + Single Download PDF -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-[10px] text-gray-500 font-sans">
            <span>1 tiket berlaku untuk 1 kali masuk (Gate Check-in)</span>
            <a href="{{ route('orders.tickets.single.pdf', ['order' => $order->booking_code, 'ticket' => $ticket->ticket_code]) }}" 
               class="no-print inline-flex items-center gap-1.5 px-3 py-1 bg-hf-purple-100 hover:bg-hf-purple-200 text-hf-purple-900 rounded-lg font-bold border border-hf-purple-900/30 transition-colors">
                <span>📄</span>
                <span>Unduh PDF Tiket Ini</span>
            </a>
        </div>
    </div>

    <!-- PERFORATION / STUB CONNECTOR -->
    <div class="relative flex md:flex-col items-center justify-between bg-purple-100 md:w-0 border-t-4 md:border-t-0 md:border-l-4 border-dashed border-hf-purple-900/60 shrink-0">
        <!-- Desktop notches -->
        <div class="hidden md:block absolute -top-4 -left-3.5 w-7 h-7 rounded-full bg-hf-purple-800 border-b-4 border-hf-purple-900"></div>
        <div class="hidden md:block absolute -bottom-4 -left-3.5 w-7 h-7 rounded-full bg-hf-purple-800 border-t-4 border-hf-purple-900"></div>
        
        <!-- Mobile notches -->
        <div class="md:hidden absolute -left-4 -top-3.5 w-7 h-7 rounded-full bg-hf-purple-800 border-r-4 border-hf-purple-900"></div>
        <div class="md:hidden absolute -right-4 -top-3.5 w-7 h-7 rounded-full bg-hf-purple-800 border-l-4 border-hf-purple-900"></div>
    </div>

    <!-- RIGHT SECTION: QR Stub (~30%) -->
    <div class="p-6 md:w-64 bg-purple-50/80 flex flex-col items-center justify-center text-center shrink-0 border-t-4 md:border-t-0 border-hf-purple-900/20">
        
        <span class="font-sans font-black text-xs uppercase tracking-widest text-hf-purple-900 mb-2">
            SCAN AT ENTRANCE
        </span>

        <!-- QR Code Image (Data URI PNG) or Error Monospace Code -->
        <div class="p-2 bg-white rounded-2xl border-2 border-hf-purple-900 shadow-hard-xs inline-block">
            @if ($qrDataUri)
                <img src="{{ $qrDataUri }}" 
                     alt="QR Code {{ $ticket->ticket_code }}" 
                     class="w-32 h-32 object-contain block mx-auto">
            @else
                <div class="w-32 h-32 flex flex-col items-center justify-center p-2 bg-amber-50 rounded-xl border border-amber-300 text-center">
                    <span class="text-[10px] font-sans font-bold text-amber-800 uppercase mb-1">Gagal Buat QR</span>
                    <span class="font-mono text-xs font-black text-hf-purple-900 break-all">{{ $ticket->ticket_code }}</span>
                </div>
            @endif
        </div>

        <!-- Unique Ticket Code -->
        <div class="mt-3">
            <span class="font-mono font-black text-xs sm:text-sm text-hf-purple-900 tracking-wider block">
                {{ $ticket->ticket_code }}
            </span>
        </div>

        <!-- Check-in Status Badge -->
        <div class="mt-2.5">
            @if ($ticket->is_used)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-500 text-white font-sans font-bold text-[11px] uppercase rounded-xl border border-hf-purple-900 shadow-xs">
                    <span>⛔</span>
                    <span>Sudah Digunakan</span>
                </span>
                <span class="text-[10px] text-gray-500 block mt-1 font-sans">
                    Scan: {{ format_wib($ticket->used_at) }}
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500 text-white font-sans font-bold text-[11px] uppercase rounded-xl border border-hf-purple-900 shadow-xs">
                    <span>✅</span>
                    <span>Siap Digunakan</span>
                </span>
            @endif
        </div>

    </div>

</div>

