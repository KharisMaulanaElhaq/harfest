<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TicketService
{
    /**
     * Issue e-tickets for a paid order.
     * Idempotent: does not regenerate tickets if already issued.
     *
     * @return Collection<int, Ticket>
     */
    public function issue(Order $order): Collection
    {
        // Jika tiket sudah terbit, kembalikan tiket yang ada (idempotent)
        if ($order->tickets()->exists()) {
            return $order->tickets;
        }

        $totalTickets = $order->quantity * $order->ticketType->people_per_ticket;
        $createdTickets = new Collection();

        for ($i = 1; $i <= $totalTickets; $i++) {
            $ticketCode = $this->generateUniqueTicketCode();

            $ticket = Ticket::create([
                'order_id' => $order->id,
                'ticket_code' => $ticketCode,
                'holder_name' => $order->user->name . ($totalTickets > 1 ? " (Orang ke-$i)" : ''),
                'is_used' => false,
                'used_at' => null,
                'checked_by' => null,
            ]);

            $createdTickets->push($ticket);
        }

        return $createdTickets;
    }

    /**
     * Generate unique ticket code: HF- + 16 random uppercase characters.
     */
    public function generateUniqueTicketCode(): string
    {
        do {
            $code = 'HF-' . strtoupper(Str::random(16));
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
