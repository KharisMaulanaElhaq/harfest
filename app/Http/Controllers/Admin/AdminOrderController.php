<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOrderController extends Controller
{
    /**
     * Display listing of all orders with filters, search, and working event scoping.
     * The current event is resolved by SetAdminEvent middleware.
     */
    public function index(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');

        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('q', ''));

        $query = Order::with(['user', 'ticketType', 'latestPayment'])->latest();

        if ($currentEvent) {
            $query->where('event_id', $currentEvent->id);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders', 'statusFilter', 'search', 'currentEvent'));
    }

    /**
     * Export filtered or all orders for working event as CSV (streamed response with UTF-8 BOM for Excel).
     * The current event is resolved by SetAdminEvent middleware.
     */
    public function export(Request $request): StreamedResponse
    {
        $currentEvent = $request->attributes->get('admin_event');

        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('q', ''));

        $query = Order::with(['user', 'ticketType', 'latestPayment', 'event'])->latest();

        if ($currentEvent) {
            $query->where('event_id', $currentEvent->id);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $eventSlug = $currentEvent ? $currentEvent->slug : 'all';
        $fileName = 'pesanan-harfest-' . $eventSlug . '-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($handle, [
                'Kode Booking',
                'Event',
                'Nama Pembeli',
                'Email',
                'No. HP',
                'Jenis Tiket',
                'Jumlah Paket',
                'Total Orang',
                'Total Harga (Rp)',
                'Status',
                'NIM / ID Civitas',
                'Bank Pengirim',
                'Nama Rekening Pengirim',
                'Tanggal Pesan',
            ]);

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $totalPeople = $order->quantity * ($order->ticketType?->people_per_ticket ?? 1);
                    $payment = $order->latestPayment;

                    fputcsv($handle, [
                        $order->booking_code,
                        $order->event?->name ?? 'Harfest',
                        $order->user?->name ?? '-',
                        $order->user?->email ?? '-',
                        $order->user?->phone ?? '-',
                        $order->ticketType?->name ?? '-',
                        $order->quantity,
                        $totalPeople,
                        $order->total_price,
                        $order->status->label(),
                        $order->student_id ?? '-',
                        $payment?->bank_name ?? '-',
                        $payment?->sender_name ?? '-',
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
