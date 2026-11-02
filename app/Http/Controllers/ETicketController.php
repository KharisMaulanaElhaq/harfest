<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\TicketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ETicketController extends Controller
{
    /**
     * Display e-tickets in browser for a paid order.
     */
    public function show(Order $order, TicketService $ticketService): View|RedirectResponse
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
     * Download e-tickets as a generated PDF file.
     */
    public function download(Order $order, TicketService $ticketService): Response|RedirectResponse
    {
        Gate::authorize('view', $order);

        if ($order->status !== OrderStatus::Paid) {
            return redirect()
                ->route('orders.show', $order->booking_code)
                ->with('error', 'E-tiket hanya dapat diunduh setelah pembayaran diverifikasi dan berstatus lunas.');
        }

        // Pastikan tiket sudah diterbitkan (idempotent)
        $ticketService->issue($order);

        $order->load(['tickets', 'ticketType.event', 'event', 'user']);
        $event = $order->event ?? $order->ticketType?->event;
        $tickets = $order->tickets;

        $pdf = Pdf::loadView('orders.pdf-tickets', compact('order', 'tickets', 'event'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('E-Ticket-Harfest-' . $order->booking_code . '.pdf');
    }
}

