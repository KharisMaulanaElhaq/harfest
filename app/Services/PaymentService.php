<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    /**
     * Upload payment proof for an order.
     */
    public function uploadProof(Order $order, UploadedFile $file, ?string $bankName = null, ?string $senderName = null): Payment
    {
        return DB::transaction(function () use ($order, $file, $bankName, $senderName) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            // Hanya boleh upload jika status pending_payment atau rejected, dan belum expired
            if (! in_array($lockedOrder->status, [OrderStatus::PendingPayment, OrderStatus::Rejected], true)) {
                throw ValidationException::withMessages([
                    'proof' => 'Pesanan ini tidak dalam status yang memungkinkan unggah bukti pembayaran.',
                ]);
            }

            if ($lockedOrder->isExpired()) {
                throw ValidationException::withMessages([
                    'proof' => 'Batas waktu pembayaran untuk pesanan ini telah berakhir.',
                ]);
            }

            // Simpan file bukti transfer ke disk privat
            $proofPath = $file->store('payments', 'local');

            // Buat record pembayaran baru
            $payment = Payment::create([
                'order_id' => $lockedOrder->id,
                'proof_path' => $proofPath,
                'bank_name' => $bankName,
                'sender_name' => $senderName,
                'status' => PaymentStatus::Pending,
                'reject_reason' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);

            // Update status pesanan ke waiting_verification
            $lockedOrder->update([
                'status' => OrderStatus::WaitingVerification,
            ]);

            return $payment;
        });
    }

    /**
     * Approve payment proof and issue e-tickets.
     */
    public function approve(Payment $payment, User $admin): Order
    {
        return DB::transaction(function () use ($payment, $admin) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->firstOrFail();

            $lockedPayment->update([
                'status' => PaymentStatus::Approved,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            $order->update([
                'status' => OrderStatus::Paid,
            ]);

            // Terbitkan E-Tiket QR
            $this->ticketService->issue($order);

            return $order;
        });
    }

    /**
     * Reject payment proof with mandatory reason.
     */
    public function reject(Payment $payment, User $admin, string $reason): Order
    {
        return DB::transaction(function () use ($payment, $admin, $reason) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->firstOrFail();

            $lockedPayment->update([
                'status' => PaymentStatus::Rejected,
                'reject_reason' => $reason,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            $order->update([
                'status' => OrderStatus::Rejected,
            ]);

            return $order;
        });
    }
}
