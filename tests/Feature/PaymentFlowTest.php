<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $admin;
    protected TicketType $ticketType;
    protected TicketType $groupTicketType;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create([
            'role' => UserRole::User,
            'phone' => '081234567890',
        ]);

        $this->otherUser = User::factory()->create([
            'role' => UserRole::User,
            'phone' => '089876543210',
        ]);

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'phone' => '08111222333',
        ]);

        $event = Event::create([
            'name' => 'Harfest 2026',
            'description' => 'Konser akbar tahunan kampus Harkat Negeri',
            'venue' => 'Lapangan Utama',
            'event_date' => now()->addDays(30),
            'is_active' => true,
        ]);

        $this->ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Umum 1 Orang',
            'description' => 'Tiket untuk 1 orang',
            'price' => 40000,
            'quota' => 100,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $this->groupTicketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Umum 3 Orang',
            'description' => 'Paket hemat untuk 3 orang',
            'price' => 100000,
            'quota' => 50,
            'people_per_ticket' => 3,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'booking_code' => 'HF-20261016-TEST',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function test_user_can_upload_payment_proof(): void
    {
        $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 800)->size(500);

        $response = $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'proof' => $file,
            'bank_name' => 'BCA',
            'sender_name' => 'Budi Santoso',
        ]);

        $response->assertRedirect(route('orders.show', $this->order->booking_code));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals(OrderStatus::WaitingVerification, $this->order->status);

        $payment = $this->order->latestPayment;
        $this->assertNotNull($payment);
        $this->assertEquals(PaymentStatus::Pending, $payment->status);
        $this->assertEquals('BCA', $payment->bank_name);
        $this->assertEquals('Budi Santoso', $payment->sender_name);

        Storage::disk('local')->assertExists($payment->proof_path);
    }

    public function test_upload_proof_requires_valid_file(): void
    {
        // 1. Missing file
        $response = $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'bank_name' => 'BCA',
        ]);
        $response->assertSessionHasErrors(['proof']);

        // 2. Disallowed mime type (txt)
        $invalidFile = UploadedFile::fake()->create('hacked.txt', 100);
        $response = $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'proof' => $invalidFile,
        ]);
        $response->assertSessionHasErrors(['proof']);

        // 3. Exceeds 2 MB (2048 KB)
        $tooLargeFile = UploadedFile::fake()->create('large_image.jpg', 3000);
        $response = $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'proof' => $tooLargeFile,
        ]);
        $response->assertSessionHasErrors(['proof']);
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        // Unauthenticated -> redirected to login
        $this->get(route('admin.payments.index'))->assertRedirect(route('login'));

        // Normal user -> 403 Forbidden
        $this->actingAs($this->user)
            ->get(route('admin.payments.index'))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->get('/admin/payments')
            ->assertForbidden();
    }

    public function test_admin_can_view_payment_list_and_details(): void
    {
        $file = UploadedFile::fake()->image('bukti.png', 400, 400);
        $path = $file->store('payments', 'local');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'proof_path' => $path,
            'bank_name' => 'Mandiri',
            'sender_name' => 'Ahmad',
            'status' => PaymentStatus::Pending,
        ]);
        $this->order->update(['status' => OrderStatus::WaitingVerification]);

        // Admin index
        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $response->assertOk();
        $response->assertSee($this->order->booking_code);
        $response->assertSee('Ahmad');

        // Admin show
        $showResponse = $this->actingAs($this->admin)->get(route('admin.payments.show', $payment->id));
        $showResponse->assertOk();
        $showResponse->assertSee($this->order->booking_code);
        $showResponse->assertSee('Mandiri');
    }

    public function test_full_rejection_and_reupload_flow(): void
    {
        // 1. User uploads initial proof
        $file = UploadedFile::fake()->image('bukti_pertama.jpg');
        $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'proof' => $file,
            'bank_name' => 'BCA',
            'sender_name' => 'Budi Santoso',
        ]);

        $this->order->refresh();
        $payment = $this->order->latestPayment;
        $this->assertEquals(OrderStatus::WaitingVerification, $this->order->status);

        // 2. Admin rejects payment with reason
        $rejectResponse = $this->actingAs($this->admin)->post(route('admin.payments.reject', $payment->id), [
            'reject_reason' => 'Nominal transfer kurang Rp 5.000, mohon periksa mutasi.',
        ]);

        $rejectResponse->assertRedirect(route('admin.payments.index'));
        $rejectResponse->assertSessionHas('success');

        $this->order->refresh();
        $payment->refresh();

        $this->assertEquals(OrderStatus::Rejected, $this->order->status);
        $this->assertEquals(PaymentStatus::Rejected, $payment->status);
        $this->assertEquals('Nominal transfer kurang Rp 5.000, mohon periksa mutasi.', $payment->reject_reason);
        $this->assertEquals($this->admin->id, $payment->verified_by);

        // 3. User views order page and sees the rejection reason
        $userView = $this->actingAs($this->user)->get(route('orders.show', $this->order->booking_code));
        $userView->assertOk();
        $userView->assertSee('Bukti Pembayaran Ditolak Panitia');
        $userView->assertSee('Nominal transfer kurang Rp 5.000, mohon periksa mutasi.');
        $userView->assertSee('Unggah Ulang Bukti Pembayaran');

        // 4. User re-uploads a new proof
        $newFile = UploadedFile::fake()->image('bukti_revisi.jpg');
        $reuploadResponse = $this->actingAs($this->user)->post(route('orders.payment', $this->order->booking_code), [
            'proof' => $newFile,
            'bank_name' => 'BCA',
            'sender_name' => 'Budi Santoso (Revisi)',
        ]);

        $reuploadResponse->assertRedirect(route('orders.show', $this->order->booking_code));

        $this->order->refresh();
        $this->assertEquals(OrderStatus::WaitingVerification, $this->order->status);

        $newPayment = $this->order->latestPayment;
        $this->assertNotEquals($payment->id, $newPayment->id);
        $this->assertEquals(PaymentStatus::Pending, $newPayment->status);
        $this->assertEquals('Budi Santoso (Revisi)', $newPayment->sender_name);
    }

    public function test_reject_requires_reason(): void
    {
        $file = UploadedFile::fake()->image('bukti.jpg');
        $path = $file->store('payments', 'local');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'proof_path' => $path,
            'status' => PaymentStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.reject', $payment->id), [
            'reject_reason' => '',
        ]);

        $response->assertSessionHasErrors(['reject_reason']);
    }

    public function test_admin_approve_marks_order_paid_and_issues_tickets(): void
    {
        // Buat pesanan grup 3 orang
        $groupOrder = Order::create([
            'booking_code' => 'HF-20261016-GROUP',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->groupTicketType->id,
            'quantity' => 2, // 2 paket x 3 orang = 6 tiket
            'total_price' => 200000,
            'status' => OrderStatus::WaitingVerification,
            'expires_at' => now()->addHours(24),
        ]);

        $file = UploadedFile::fake()->image('bukti_group.jpg');
        $path = $file->store('payments', 'local');

        $payment = Payment::create([
            'order_id' => $groupOrder->id,
            'proof_path' => $path,
            'status' => PaymentStatus::Pending,
        ]);

        // Admin approves payment
        $response = $this->actingAs($this->admin)->post(route('admin.payments.approve', $payment->id));
        $response->assertRedirect(route('admin.payments.index'));

        $groupOrder->refresh();
        $payment->refresh();

        $this->assertEquals(OrderStatus::Paid, $groupOrder->status);
        $this->assertEquals(PaymentStatus::Approved, $payment->status);
        $this->assertEquals($this->admin->id, $payment->verified_by);

        // Verify tickets issued
        $this->assertCount(6, $groupOrder->tickets);
        foreach ($groupOrder->tickets as $ticket) {
            $this->assertStringStartsWith('HF-', $ticket->ticket_code);
            $this->assertFalse($ticket->is_used);
        }
    }

    public function test_file_authorization_for_payment_proof(): void
    {
        $file = UploadedFile::fake()->image('bukti_privat.jpg');
        $path = $file->store('payments', 'local');

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'proof_path' => $path,
            'status' => PaymentStatus::Pending,
        ]);

        // Owner can view
        $this->actingAs($this->user)
            ->get(route('files.payment', $payment->id))
            ->assertOk();

        // Admin can view
        $this->actingAs($this->admin)
            ->get(route('files.payment', $payment->id))
            ->assertOk();

        // Other user is forbidden (403)
        $this->actingAs($this->otherUser)
            ->get(route('files.payment', $payment->id))
            ->assertForbidden();

        // Unauthenticated is redirected to login
        auth()->logout();
        $this->get(route('files.payment', $payment->id))
            ->assertRedirect(route('login'));
    }
}
