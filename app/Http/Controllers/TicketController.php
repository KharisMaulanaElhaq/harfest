<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\EventService;
use App\Services\TicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Download all e-tickets in an order as a single PDF.
     */
    public function downloadOrderTicketsPdf(Order $order, TicketService $ticketService): Response
    {
        Gate::authorize('view', $order);

        if ($order->status !== OrderStatus::Paid) {
            return redirect()
                ->route('orders.show', $order->booking_code)
                ->with('error', 'E-tiket hanya dapat diunduh setelah pembayaran diverifikasi dan berstatus lunas.');
        }

        $ticketService->issue($order);

        $order->load(['tickets', 'ticketType.event', 'event', 'user']);
        $event = $order->event ?? $order->ticketType?->event;

        $logoPath = public_path('images/brand/harfest-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('etickets.pdf', [
            'order' => $order,
            'tickets' => $order->tickets,
            'event' => $event,
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);

        return $pdf->download("e-tiket-{$order->booking_code}.pdf");
    }

    /**
     * Download a single ticket as PDF.
     */
    public function downloadSingleTicketPdf(Order $order, Ticket $ticket, TicketService $ticketService): Response
    {
        Gate::authorize('view', $order);

        if ($ticket->order_id !== $order->id) {
            abort(404, 'Tiket tidak ditemukan pada pesanan ini.');
        }

        if ($order->status !== OrderStatus::Paid) {
            return redirect()
                ->route('orders.show', $order->booking_code)
                ->with('error', 'E-tiket hanya dapat diunduh setelah pembayaran diverifikasi dan berstatus lunas.');
        }

        $order->load(['ticketType.event', 'event', 'user']);
        $event = $order->event ?? $order->ticketType?->event;

        $logoPath = public_path('images/brand/harfest-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('etickets.pdf', [
            'order' => $order,
            'tickets' => collect([$ticket]),
            'event' => $event,
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);

        return $pdf->download("e-tiket-{$ticket->ticket_code}.pdf");
    }
}
