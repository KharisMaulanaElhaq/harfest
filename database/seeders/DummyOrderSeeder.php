<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vol1 = Event::where('slug', 'vol-1')->first();
        $user = User::first();
        $admin = User::where('role', \App\Enums\UserRole::Admin)->first() ?? $user;

        if (! $vol1 || ! $user) {
            return;
        }

        $ticketType = $vol1->ticketTypes()->first();
        if (! $ticketType) {
            return;
        }

        // Create 2 paid dummy orders for Vol 1
        for ($i = 1; $i <= 2; $i++) {
            $bookingCode = "HF1-20251125-000{$i}";
            if (Order::where('booking_code', $bookingCode)->exists()) {
                continue;
            }

            $order = Order::create([
                'booking_code' => $bookingCode,
                'event_id' => $vol1->id,
                'user_id' => $user->id,
                'ticket_type_id' => $ticketType->id,
                'quantity' => 1,
                'total_price' => $ticketType->price,
                'student_id' => $ticketType->requires_student_id ? 'NIM12345678' : null,
                'status' => OrderStatus::Paid,
                'expires_at' => now()->subMonths(10),
                'created_at' => now()->subMonths(11),
                'updated_at' => now()->subMonths(11),
            ]);

            Payment::create([
                'order_id' => $order->id,
                'proof_path' => 'payments/dummy-proof.jpg',
                'bank_name' => 'BCA',
                'sender_name' => 'Demo User',
                'status' => PaymentStatus::Approved,
                'verified_by' => $admin->id,
                'verified_at' => now()->subMonths(11),
            ]);

            Ticket::create([
                'order_id' => $order->id,
                'ticket_code' => 'HF-VOL1-TEST-' . strtoupper(Str::random(8)),
                'holder_name' => $user->name,
                'is_used' => true,
                'used_at' => now()->subMonths(11)->addDays(4),
                'checked_by' => $admin->id,
            ]);
        }
    }
}
