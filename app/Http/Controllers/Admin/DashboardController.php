<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin overview dashboard filtered by the active working event.
     * The current event is resolved by SetAdminEvent middleware and injected
     * via request attributes — no fallback logic needed here.
     */
    public function index(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');

        $eventId = $currentEvent?->id;

        // 1. Total Pendapatan (Hanya pesanan berstatus paid untuk event terpilih)
        $totalRevenue = $eventId
            ? Order::where('event_id', $eventId)->where('status', OrderStatus::Paid)->sum('total_price')
            : 0;

        // 2. Total Tiket Terjual (paid)
        $totalTicketsSold = $eventId
            ? (int) Order::where('orders.event_id', $eventId)
                ->where('orders.status', OrderStatus::Paid)
                ->join('ticket_types', 'orders.ticket_type_id', '=', 'ticket_types.id')
                ->sum(DB::raw('orders.quantity * ticket_types.people_per_ticket'))
            : 0;

        // 3. Jumlah pesanan menunggu verifikasi
        $waitingVerificationCount = $eventId
            ? Order::where('event_id', $eventId)->where('status', OrderStatus::WaitingVerification)->count()
            : 0;

        // 4. Total pesanan lunas
        $paidOrdersCount = $eventId
            ? Order::where('event_id', $eventId)->where('status', OrderStatus::Paid)->count()
            : 0;

        // 5. Sisa kuota per jenis tiket untuk event terpilih
        $ticketTypes = $currentEvent
            ? $currentEvent->ticketTypes()->withQuotaStats()->orderBy('price')->get()
            : collect();

        // 6. Daftar 5 pesanan terbaru untuk event terpilih
        $recentOrders = $eventId
            ? Order::where('event_id', $eventId)->with(['user', 'ticketType'])->latest()->take(5)->get()
            : collect();

        return view('admin.dashboard', compact(
            'currentEvent',
            'totalRevenue',
            'totalTicketsSold',
            'waitingVerificationCount',
            'paidOrdersCount',
            'ticketTypes',
            'recentOrders'
        ));
    }
}
