<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Services\EventService;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of active ticket types for the active event.
     */
    public function index(EventService $eventService): View
    {
        $event = $eventService->active();

        $ticketTypes = $event 
            ? $event->ticketTypes()->withQuotaStats()->where('is_active', true)->orderBy('price')->get() 
            : collect();

        return view('tickets.index', compact('event', 'ticketTypes'));
    }

    /**
     * Display e-tickets for a paid order.
     */
    public function showOrderTickets(Order $order, TicketService $ticketService): View|RedirectResponse
    {
        Gate::authorize('view', $order);

        if ($order->status !== OrderStatus::Paid) {
            return redirect()
                ->route('orders.show', $order->booking_code)
                ->with('error', 'E-tiket hanya dapat diakses setelah pembayaran diverifikasi dan berstatus lunas.');
        }

        // Pastikan tiket sudah diterbitkan (idempotent)
        $ticketService->issue($order);

        $order->load(['tickets', 'ticketType.event', 'event', 'user']);
        $event = $order->event ?? $order->ticketType?->event;

        return view('orders.tickets', [
            'order' => $order,
            'tickets' => $order->tickets,
            'event' => $event,
        ]);
    }
}
