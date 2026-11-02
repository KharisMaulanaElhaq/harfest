<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket Harfest - {{ $order->booking_code }}</title>
    <style>
        @page {
            margin: 20px 25px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2D0A4E;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #FFD000;
        }
        .header-title {
            font-size: 20px;
            font-weight: 900;
            color: #4D0570;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .header-subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }
        .ticket-box {
            border: 2px solid #4D0570;
            border-radius: 12px;
            margin-bottom: 25px;
            background: #ffffff;
            page-break-inside: avoid;
            width: 100%;
        }
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stub-main {
            padding: 16px 20px;
            vertical-align: top;
            width: 68%;
        }
        .stub-divider {
            border-left: 2px dashed #9E0A7B;
            width: 1px;
            background-color: #faf5ff;
        }
        .stub-qr {
            padding: 16px;
            text-align: center;
            vertical-align: middle;
            width: 32%;
            background-color: #faf5ff;
        }
        .badge-type {
            display: inline-block;
            background-color: #FFD000;
            color: #2D0A4E;
            font-size: 10px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .ticket-title {
            font-size: 16px;
            font-weight: bold;
            color: #4D0570;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .holder-label {
            font-size: 9px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 8px;
        }
        .holder-name {
            font-size: 15px;
            font-weight: bold;
            color: #2D0A4E;
            margin: 2px 0;
        }
        .info-grid {
            width: 100%;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }
        .info-col {
            font-size: 10px;
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            color: #888;
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .info-val {
            font-weight: bold;
            color: #333;
            margin-top: 1px;
        }
        .qr-wrapper {
            margin: 0 auto;
            text-align: center;
        }
        .ticket-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            font-weight: bold;
            color: #2D0A4E;
            letter-spacing: 1px;
            margin-top: 8px;
            background: #fff;
            padding: 2px 4px;
            border-radius: 4px;
            display: inline-block;
        }
        .qr-label {
            font-size: 8px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .rules-footer {
            margin-top: 10px;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
        .watermark {
            font-size: 9px;
            color: #DE037A;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <h1 class="header-title">{{ $event?->name ?? 'Harfest 2026' }}</h1>
        <div class="header-subtitle">
            E-Tiket Resmi Konser Kampus Universitas Harkat Negeri • Kode Pesanan: <strong>{{ $order->booking_code }}</strong>
        </div>
    </div>

    @foreach ($tickets as $index => $ticket)
        <div class="ticket-box">
            <table class="ticket-table">
                <tr>
                    <!-- LEFT STUB -->
                    <td class="stub-main">
                        <div>
                            <span class="badge-type">{{ $order->ticketType->name }}</span>
                            <span class="watermark">• Tiket #{{ $index + 1 }} dari {{ $tickets->count() }}</span>
                        </div>

                        <div class="holder-label">Nama Pemegang Tiket:</div>
                        <div class="holder-name">{{ $ticket->holder_name }}</div>

                        <table class="info-grid">
                            <tr>
                                <td class="info-col">
                                    <div class="info-label">Tanggal & Waktu:</div>
                                    <div class="info-val">
                                        {{ $event?->event_date?->translatedFormat('l, d F Y') ?? 'Sabtu, 28 November 2026' }}
                                    </div>
                                    <div style="font-size: 9px; color: #666;">Open Gate: 14:00 WIB</div>
                                </td>
                                <td class="info-col">
                                    <div class="info-label">Lokasi Acara:</div>
                                    <div class="info-val">
                                        {{ $event?->venue ?? 'Lapangan Utama Universitas Harkat Negeri' }}
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="rules-footer">
                            * Tiket hanya berlaku untuk 1 (satu) kali akses masuk gerbang acara. Tunjukkan QR code kepada petugas.
                        </div>
                    </td>

                    <!-- PERFORATION DIVIDER -->
                    <td class="stub-divider"></td>

                    <!-- RIGHT QR STUB -->
                    <td class="stub-qr">
                        <div class="qr-label">Pindai di Gerbang Masuk</div>

                        <div class="qr-wrapper">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(110)->color(77, 5, 112)->generate($ticket->ticket_code) !!}
                        </div>

                        <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

</body>
</html>

