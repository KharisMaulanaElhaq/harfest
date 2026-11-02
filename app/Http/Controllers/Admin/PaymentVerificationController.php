<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentVerificationController extends Controller
{
    /**
     * Display a listing of payments requiring verification, filtered by working event.
     * The current event is resolved by SetAdminEvent middleware.
     */
    public function index(Request $request): View
    {
        $currentEvent = $request->attributes->get('admin_event');

        $statusFilter = $request->query('status', 'pending');

        $query = Payment::with(['order.user', 'order.ticketType', 'order.event', 'verifier'])->latest();

        if ($currentEvent) {
            $query->whereHas('order', fn ($q) => $q->where('event_id', $currentEvent->id));
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $payments = $query->paginate(15)->withQueryString();

        $pendingQuery = Payment::where('status', PaymentStatus::Pending);
        if ($currentEvent) {
            $pendingQuery->whereHas('order', fn ($q) => $q->where('event_id', $currentEvent->id));
        }
        $pendingCount = $pendingQuery->count();

        return view('admin.payments.index', compact('payments', 'statusFilter', 'pendingCount', 'currentEvent'));
    }

    /**
     * Display the payment details with proof preview and civitas info.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.ticketType', 'order.event', 'verifier']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve the payment, set order to paid, and issue e-tickets.
     */
    public function approve(Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $order = $paymentService->approve($payment, Auth::user());

        return redirect()
            ->route('admin.payments.index')
            ->with('success', "Pembayaran untuk pesanan {$order->booking_code} berhasil disetujui! E-Tiket telah terbit.");
    }

    /**
     * Reject the payment with mandatory reason.
     */
    public function reject(RejectPaymentRequest $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $order = $paymentService->reject($payment, Auth::user(), $request->input('reject_reason'));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', "Pembayaran untuk pesanan {$order->booking_code} telah ditolak.");
    }
}
