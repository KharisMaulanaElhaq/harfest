<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Tiket {{ $order->booking_code }} - {{ $event?->name ?? 'Harfest' }}</title>
    <style>
        @page {
            margin: 20px;
            size: a4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #2D0A4E;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        .page-ticket {
            padding: 10px 0 25px 0;
        }
        .page-break {
            page-break-after: always;
        }
        /* Outer ticket card table */
        .ticket-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 3px solid #4D0570;
            border-radius: 16px;
            background-color: #ffffff;
            margin-bottom: 20px;
        }
        .stub-main {
            width: 68%;
            padding: 18px 20px;
            vertical-align: top;
            background-color: #ffffff;
        }
        .stub-separator {
            width: 1px;
            border-left: 2px dashed #9E0A7B;
            padding: 0;
            background-color: #faf5ff;
        }
        .stub-qr {
            width: 31%;
            padding: 18px 14px;
            vertical-align: middle;
            text-align: center;
            background-color: #faf5ff;
        }
        /* Header table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #f3e8ff;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .logo-img {
            height: 38px;
            width: auto;
            vertical-align: middle;
        }
        .brand-title {
            font-size: 16px;
            font-weight: bold;
            color: #4D0570;
            text-transform: uppercase;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #DE037A;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .ticket-badge {
            display: inline-block;
            background-color: #FFD000;
            color: #4D0570;
            font-weight: bold;
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1.5px solid #4D0570;
            text-transform: uppercase;
        }
        /* Holder info */
        .holder-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .holder-name {
            font-size: 20px;
            font-weight: bold;
            color: #4D0570;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .holder-sub {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 12px;
        }
        /* Event details table */
        .details-table {
            width: 100%;
            border-top: 1px dashed #d1d5db;
            padding-top: 10px;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .detail-cell {
            width: 50%;
            vertical-align: top;
            font-size: 10px;
        }
        .detail-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
            display: block;
        }
        .detail-value {
            font-size: 11px;
            font-weight: bold;
            color: #111827;
            display: block;
            margin-top: 1px;
        }
        .detail-sub {
            font-size: 9px;
            color: #4b5563;
        }
        .footer-note {
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 6px;
            margin-top: 8px;
        }
        /* QR section */
        .qr-header {
            font-size: 9px;
            font-weight: bold;
            color: #4D0570;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .qr-box {
            background-color: #ffffff;
            border: 2px solid #4D0570;
            border-radius: 12px;
            padding: 8px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .qr-img {
            width: 125px;
            height: 125px;
            display: block;
            margin: 0 auto;
        }
        .qr-fallback {
            width: 125px;
            height: 125px;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-family: monospace;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            padding-top: 45px;
            box-sizing: border-box;
        }
        .ticket-code {
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            color: #4D0570;
            margin-top: 4px;
            letter-spacing: 1px;
        }
        .status-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 6px;
            border: 1px solid #4D0570;
        }
        .status-ready {
            background-color: #10b981;
            color: #ffffff;
        }
        .status-used {
            background-color: #ef4444;
            color: #ffffff;
        }
        .scan-time {
            font-size: 8px;
            color: #6b7280;
            margin-top: 3px;
        }
    </style>
</head>
<body>

    @foreach ($tickets as $index => $ticket)
        @php
            $qrService = app(\App\Services\QrService::class);
            $qrDataUri = $qrService->dataUri($ticket->ticket_code, 'png', 200);
            $eventObj = $event ?? $order->event ?? $order->ticketType?->event;
        @endphp

        <div class="page-ticket {{ ! $loop->last ? 'page-break' : '' }}">
            
            <table class="ticket-table" cellpadding="0" cellspacing="0">
                <tr>
                    <!-- LEFT STUB: Concert & Holder Details -->
                    <td class="stub-main">
                        
                        <!-- Header: Logo & Category -->
                        <table class="header-table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="vertical-align: middle;">
                                    @if (!empty($logoDataUri))
                                        <img src="{{ $logoDataUri }}" alt="Harfest Logo" class="logo-img">
                                    @else
                                        <h2 class="brand-title">{{ $eventObj?->name ?? 'HARFEST 2026' }}</h2>
                                    @endif
                                    <div class="brand-subtitle">{{ $eventObj?->tagline ?? 'Universitas Harkat Negeri' }}</div>
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <span class="ticket-badge">{{ $order->ticketType->name }}</span>
                                </td>
                            </tr>
                        </table>

                        <!-- Holder Details -->
                        <div class="holder-label">Nama Pemegang Tiket:</div>
                        <div class="holder-name">{{ $ticket->holder_name }}</div>
                        <div class="holder-sub">
                            Tiket #{{ $index + 1 }} dari {{ $tickets->count() }} tiket pesanan • Kode Pesanan: {{ $order->booking_code }}
                        </div>

                        <!-- Event Details Grid -->
                        <table class="details-table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="detail-cell">
                                    <span class="detail-label">Tanggal & Waktu:</span>
                                    <span class="detail-value">
                                        {{ $eventObj?->event_date ? format_wib($eventObj->event_date) : 'Sabtu, 28 November 2026, 14:00 WIB' }}
                                    </span>
                                    <span class="detail-sub">Open Gate: 14:00 WIB | Show: 15:00 WIB</span>
                                </td>
                                <td class="detail-cell" style="padding-left: 14px;">
                                    <span class="detail-label">Lokasi Acara:</span>
                                    <span class="detail-value">{{ $eventObj?->venue ?? 'Lapangan Utama Kampus' }}</span>
                                    <span class="detail-sub">Universitas Harkat Negeri</span>
                                </td>
                            </tr>
                        </table>

                        <!-- Footer notes -->
                        <div class="footer-note">
                            1 tiket berlaku untuk 1 kali akses masuk Gate Check-in • Harfest Official Ticketing System • Diterbitkan: {{ format_wib(now()) }}
                        </div>

                    </td>

                    <!-- Dashed Perforation Separator -->
                    <td class="stub-separator"></td>

                    <!-- RIGHT STUB: QR Code & Validation -->
                    <td class="stub-qr">
                        
                        <div class="qr-header">SCAN AT ENTRANCE</div>

                        <div class="qr-box">
                            @if ($qrDataUri)
                                <img src="{{ $qrDataUri }}" alt="QR Code" class="qr-img">
                            @else
                                <div class="qr-fallback">
                                    QR GAGAL<br>
                                    {{ $ticket->ticket_code }}
                                </div>
                            @endif
                        </div>

                        <div class="ticket-code">{{ $ticket->ticket_code }}</div>

                        <div>
                            @if ($ticket->is_used)
                                <span class="status-pill status-used">Sudah Digunakan</span>
                                <div class="scan-time">Scan: {{ format_wib($ticket->used_at) }}</div>
                            @else
                                <span class="status-pill status-ready">Siap Digunakan</span>
                            @endif
                        </div>

                    </td>
                </tr>
            </table>

        </div>
    @endforeach

</body>
</html>

