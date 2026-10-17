<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\TicketType;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index(Request $request): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['ticketType.event', 'latestPayment'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(TicketType $ticketType): View|RedirectResponse
    {
        if (! $ticketType->is_active) {
            return redirect()->route('tickets.index')->with('error', 'Kategori tiket ini sedang tidak aktif.');
        }

        if ($ticketType->remaining_quota <= 0) {
            return redirect()->route('tickets.index')->with('error', 'Maaf, kuota tiket kategori ini sudah habis (Sold Out).');
        }

        $event = $ticketType->event;

        return view('orders.create', compact('ticketType', 'event'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request, OrderService $orderService): RedirectResponse
    {
        $ticketType = TicketType::findOrFail($request->integer('ticket_type_id'));

        $order = $orderService->create(
            user: $request->user(),
            type: $ticketType,
            qty: $request->integer('quantity'),
            civitasData: [
                'student_id' => $request->input('student_id'),
                'student_card' => $request->file('student_card'),
            ]
        );

        return redirect()
            ->route('orders.show', $order->booking_code)
            ->with('success', 'Pesanan berhasil dibuat! Segera selesaikan pembayaran sebelum batas waktu berakhir.');
    }

    /**
     * Display the specified order details and payment info.
     */
    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['ticketType.event', 'payments' => fn ($q) => $q->latest(), 'tickets']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel the specified pending order.
     */
    public function cancel(Order $order, OrderService $orderService): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        $orderService->cancel($order, Auth::user());

        return redirect()
            ->route('orders.show', $order->booking_code)
            ->with('success', 'Pesanan ' . $order->booking_code . ' berhasil dibatalkan. Kuota tiket telah dikembalikan.');
    }
}
