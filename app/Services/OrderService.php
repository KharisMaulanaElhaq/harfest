<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order with DB transaction, row lock, quota check, and server calculation.
     *
     * @param  array{student_id?: ?string, student_card?: ?UploadedFile}|null  $civitasData
     */
    public function create(User $user, TicketType $type, int $qty, ?array $civitasData = null): Order
    {
        return DB::transaction(function () use ($user, $type, $qty, $civitasData) {
            // 1. Kunci baris jenis tiket agar tidak oversell
            $lockedType = TicketType::with('event')->whereKey($type->id)->lockForUpdate()->firstOrFail();

            // 2. Validasi apakah penjualan tiket pada event dibuka
            if (! $lockedType->event || ! $lockedType->event->isSalesOpen()) {
                throw ValidationException::withMessages([
                    'event' => 'Penjualan tiket untuk event ini sedang tidak dibuka atau sudah ditutup.',
                ]);
            }

            // 3. Validasi aktif dan sisa kuota
            $used = Order::where('ticket_type_id', $lockedType->id)
                ->whereIn('status', OrderStatus::countingStatuses())
                ->sum('quantity');

            $remaining = $lockedType->quota - $used;

            if (! $lockedType->is_active || $qty > $remaining) {
                throw ValidationException::withMessages([
                    'quantity' => 'Sisa kuota tidak mencukupi untuk jumlah yang diminta (' . max(0, $remaining) . ' paket tersisa).',
                ]);
            }

            // Simpan KTM civitas jika jenis tiket mewajibkan kartu identitas
            $studentId = null;
            $studentCardPath = null;
            if ($lockedType->requires_student_id) {
                $studentId = $civitasData['student_id'] ?? null;
                $studentCard = $civitasData['student_card'] ?? null;

                if (! $studentId || ! $studentCard instanceof UploadedFile) {
                    throw ValidationException::withMessages([
                        'student_id' => 'NIM dan foto KTM wajib diunggah untuk tiket Civitas.',
                    ]);
                }

                $studentCardPath = $studentCard->store('student-cards', 'local');
            }

            $edition = $lockedType->event->edition ?? 1;

            // 4. Buat order, total dihitung server
            return Order::create([
                'booking_code' => $this->generateBookingCode($edition),
                'event_id' => $lockedType->event_id,
                'user_id' => $user->id,
                'ticket_type_id' => $lockedType->id,
                'quantity' => $qty,
                'total_price' => $lockedType->price * $qty,
                'student_id' => $studentId,
                'student_card_path' => $studentCardPath,
                'status' => OrderStatus::PendingPayment,
                'expires_at' => now()->addHours(config('harfest.payment_deadline_hours', 24)),
            ]);
        });
    }

    /**
     * Cancel an order (only if pending_payment).
     */
    public function cancel(Order $order, User $user): Order
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status !== OrderStatus::PendingPayment) {
                throw ValidationException::withMessages([
                    'order' => 'Hanya pesanan berstatus menunggu pembayaran yang dapat dibatalkan.',
                ]);
            }

            $lockedOrder->update([
                'status' => OrderStatus::Cancelled,
            ]);

            return $lockedOrder;
        });
    }

    /**
     * Generate unique booking code in format HF{edisi}-YYYYMMDD-XXXX.
     */
    public function generateBookingCode(?int $edition = null): string
    {
        $prefix = 'HF' . ($edition ?? 1) . '-' . date('Ymd') . '-';

        do {
            $code = $prefix . strtoupper(Str::random(4));
        } while (Order::where('booking_code', $code)->exists());

        return $code;
    }
}
