<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckinService
{
    /**
     * Verify ticket code and perform check-in for specific event.
     *
     * @return array{result: string, message: string, ticket: ?Ticket, holder_name?: ?string, ticket_type?: ?string, booking_code?: ?string, event_name?: ?string, used_at?: ?\Carbon\CarbonInterface, checked_by?: ?string}
     */
    public function verify(string $code, User $staff, ?Event $event = null): array
    {
        $code = trim(strtoupper($code));

        if (empty($code)) {
            return [
                'result' => 'invalid',
                'message' => 'Kode tiket tidak boleh kosong.',
                'ticket' => null,
            ];
        }

        return DB::transaction(function () use ($code, $staff, $event) {
            $ticket = Ticket::with(['order.user', 'order.ticketType', 'order.event', 'checker'])
                ->where('ticket_code', $code)
                ->lockForUpdate()
                ->first();

            // 1. Ticket not found
            if (! $ticket) {
                return [
                    'result' => 'invalid',
                    'message' => "Tiket dengan kode [{$code}] tidak ditemukan dalam sistem.",
                    'ticket' => null,
                ];
            }

            // 2. Order not paid
            if ($ticket->order->status !== OrderStatus::Paid) {
                return [
                    'result' => 'invalid',
                    'message' => "Pesanan untuk tiket ini belum berstatus lunas (Status: {$ticket->order->status->label()}).",
                    'ticket' => $ticket,
                ];
            }

            // 3. Event check: wrong_event if ticket belongs to a different event
            if ($event && $ticket->order->event_id !== $event->id) {
                $ticketEventName = $ticket->order->event->name ?? 'Event Lain';

                return [
                    'result' => 'wrong_event',
                    'message' => "Tiket ini terdaftar untuk {$ticketEventName}, bukan untuk event {$event->name}.",
                    'ticket' => $ticket,
                    'holder_name' => $ticket->holder_name,
                    'ticket_type' => $ticket->order->ticketType?->name,
                    'booking_code' => $ticket->order->booking_code,
                    'event_name' => $ticketEventName,
                ];
            }

            // 4. Ticket already used
            if ($ticket->is_used) {
                $usedAtFormatted = format_wib($ticket->used_at);
                $checkerName = $ticket->checker?->name ?? 'Petugas';

                return [
                    'result' => 'used',
                    'message' => "Tiket sudah pernah digunakan pada {$usedAtFormatted} oleh {$checkerName}.",
                    'ticket' => $ticket,
                    'holder_name' => $ticket->holder_name,
                    'ticket_type' => $ticket->order->ticketType?->name,
                    'booking_code' => $ticket->order->booking_code,
                    'event_name' => $ticket->order->event?->name,
                    'used_at' => $ticket->used_at,
                    'checked_by' => $checkerName,
                ];
            }

            // 5. Success check-in
            $now = now();
            $ticket->update([
                'is_used' => true,
                'used_at' => $now,
                'checked_by' => $staff->id,
            ]);

            return [
                'result' => 'success',
                'message' => 'Check-in berhasil! Tiket valid.',
                'ticket' => $ticket,
                'holder_name' => $ticket->holder_name,
                'ticket_type' => $ticket->order->ticketType?->name,
                'booking_code' => $ticket->order->booking_code,
                'event_name' => $ticket->order->event?->name,
                'used_at' => $now,
                'checked_by' => $staff->name,
            ];
        });
    }
}
