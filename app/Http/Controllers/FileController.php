<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Serve private payment proof file with authorization check.
     */
    public function payment(Payment $payment): mixed
    {
        $user = Auth::user();

        if (! $user || ($user->id !== $payment->order->user_id && ! $user->isAdmin())) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti pembayaran ini.');
        }

        if (! $payment->proof_path || ! Storage::disk('local')->exists($payment->proof_path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('local')->response($payment->proof_path);
    }

    /**
     * Serve private student card (KTM) file with authorization check.
     */
    public function studentCard(Order $order): mixed
    {
        Gate::authorize('view', $order);

        if (! $order->student_card_path || ! Storage::disk('local')->exists($order->student_card_path)) {
            abort(404, 'File kartu identitas tidak ditemukan.');
        }

        return Storage::disk('local')->response($order->student_card_path);
    }
}
