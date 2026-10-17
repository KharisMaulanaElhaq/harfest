<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadProofRequest;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PaymentProofController extends Controller
{
    /**
     * Store payment proof for an order.
     */
    public function store(UploadProofRequest $request, Order $order, PaymentService $paymentService): RedirectResponse
    {
        Gate::authorize('view', $order);

        $paymentService->uploadProof(
            order: $order,
            file: $request->file('proof'),
            bankName: $request->input('bank_name'),
            senderName: $request->input('sender_name')
        );

        return redirect()
            ->route('orders.show', $order->booking_code)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Tim panitia akan segera memverifikasi pesanan Anda.');
    }
}
