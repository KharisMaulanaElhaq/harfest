<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArchitectureCoreScenariosTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private User $userB;
    private User $admin;
    private Event $event;
    private TicketType $singleTicket;
    private TicketType $groupTicket;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->userA = User::factory()->create([
            'name' => 'User Alfa',
            'email' => 'user.a@harfest.test',
            'phone' => '081234567890',
            'role' => UserRole::User,
        ]);

        $this->userB = User::factory()->create([
            'name' => 'User Beta',
            'email' => 'user.b@harfest.test',
            'phone' => '081298765432',
            'role' => UserRole::User,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Harfest',
            'email' => 'admin.test@harfest.test',
            'phone' => '08111222333',
            'role' => UserRole::Admin,
        ]);

        $this->event = Event::create([
            'name' => 'Harfest 2026',
            'description' => 'Campus Music Festival',
            'venue' => 'Lapangan Utama',
            'event_date' => now()->addMonth(),
            'status' => EventStatus::OnSale,
        ]);

        $this->singleTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Tiket Umum 1 Orang',
            'description' => 'Single entry',
            'price' => 40000,
            'quota' => 10,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $this->groupTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Tiket Umum 3 Orang',
            'description' => 'Bundle 3 pax',
            'price' => 100000,
            'quota' => 5,
            'people_per_ticket' => 3,
            'requires_student_id' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Skenario 1: User bisa register dan login.
     */
    public function test_scenario_1_user_bisa_register_dan_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@harfest.test',
            'phone' => '081399887766',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi@harfest.test',
            'phone' => '081399887766',
            'role' => UserRole::User->value,
        ]);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // Login kembali
        $loginResponse = $this->post('/login', [
            'email' => 'budi@harfest.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $loginResponse->assertRedirect(route('orders.index'));
    }

    /**
     * Skenario 2: User tidak login diarahkan ke login saat mengakses /orders.
     */
    public function test_scenario_2_user_tidak_login_diarahkan_ke_login_saat_mengakses_orders(): void
    {
        $response = $this->get('/orders');
        $response->assertRedirect('/login');
    }

    /**
     * Skenario 3: Buat pesanan sukses: total_price benar, status pending_payment, kode booking unik.
     */
    public function test_scenario_3_buat_pesanan_sukses_total_price_benar_status_pending_payment_kode_booking_unik(): void
    {
        $qty = 2;
        $expectedTotal = $this->singleTicket->price * $qty; // 40,000 * 2 = 80,000

        $response = $this->actingAs($this->userA)->post('/orders', [
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => $qty,
        ]);

        $order = Order::where('user_id', $this->userA->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($expectedTotal, $order->total_price);
        $this->assertEquals(OrderStatus::PendingPayment, $order->status);
        $this->assertMatchesRegularExpression('/^HF\d*-\d{8}-[A-Z0-9]{4}$/', $order->booking_code);

        $response->assertRedirect(route('orders.show', $order->booking_code));
    }

    /**
     * Skenario 4: Buat pesanan gagal jika melebihi kuota.
     */
    public function test_scenario_4_buat_pesanan_gagal_jika_melebihi_kuota(): void
    {
        // Kuota groupTicket = 5, request = 6
        $response = $this->actingAs($this->userA)->post('/orders', [
            'ticket_type_id' => $this->groupTicket->id,
            'quantity' => 6,
        ]);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertEquals(0, Order::where('ticket_type_id', $this->groupTicket->id)->count());
    }

    /**
     * Skenario 5: Dua pesanan beruntun pada kuota tersisa 1: hanya satu berhasil.
     */
    public function test_scenario_5_dua_pesanan_beruntun_pada_kuota_tersisa_1_hanya_satu_berhasil(): void
    {
        $limitedTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Tiket Limited',
            'description' => 'Hanya 1 kuota tersisa',
            'price' => 50000,
            'quota' => 1,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        // Pesanan 1 oleh User A -> harus berhasil
        $responseA = $this->actingAs($this->userA)->post('/orders', [
            'ticket_type_id' => $limitedTicket->id,
            'quantity' => 1,
        ]);
        $responseA->assertSessionHasNoErrors();
        $this->assertEquals(0, $limitedTicket->fresh()->remaining_quota);

        // Pesanan 2 oleh User B pada sisa kuota 0 -> harus gagal
        $responseB = $this->actingAs($this->userB)->post('/orders', [
            'ticket_type_id' => $limitedTicket->id,
            'quantity' => 1,
        ]);
        $responseB->assertSessionHasErrors(['quantity']);

        // Hanya 1 pesanan di database
        $this->assertEquals(1, Order::where('ticket_type_id', $limitedTicket->id)->count());
    }

    /**
     * Skenario 6: Upload bukti: format salah ditolak, file > 2 MB ditolak, sukses mengubah status ke waiting_verification.
     */
    public function test_scenario_6_upload_bukti_format_salah_dan_file_besar_ditolak_sukses_ubah_status(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261128-UP01',
            'user_id' => $this->userA->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addDay(),
        ]);

        // 1. Format salah (.txt) ditolak
        $invalidFile = UploadedFile::fake()->create('proof.txt', 100, 'text/plain');
        $resInvalid = $this->actingAs($this->userA)->post("/orders/{$order->booking_code}/payment", [
            'proof' => $invalidFile,
        ]);
        $resInvalid->assertSessionHasErrors(['proof']);

        // 2. File > 2 MB ditolak (2049 KB)
        $largeFile = UploadedFile::fake()->create('proof_large.png', 2500, 'image/png');
        $resLarge = $this->actingAs($this->userA)->post("/orders/{$order->booking_code}/payment", [
            'proof' => $largeFile,
        ]);
        $resLarge->assertSessionHasErrors(['proof']);

        // 3. File valid jpg <= 2 MB sukses
        $validFile = UploadedFile::fake()->image('valid_proof.jpg', 800, 600)->size(500);
        $resValid = $this->actingAs($this->userA)->post("/orders/{$order->booking_code}/payment", [
            'proof' => $validFile,
            'bank_name' => 'BCA',
            'sender_name' => 'Alfa Test',
        ]);
        $resValid->assertRedirect(route('orders.show', $order->booking_code));

        $this->assertEquals(OrderStatus::WaitingVerification, $order->fresh()->status);
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(PaymentStatus::Pending, $payment->status);
        Storage::disk('local')->assertExists($payment->proof_path);
    }

    /**
     * Skenario 7: User A tidak bisa melihat pesanan atau bukti bayar user B.
     */
    public function test_scenario_7_user_a_tidak_bisa_melihat_pesanan_atau_bukti_bayar_user_b(): void
    {
        $orderB = Order::create([
            'booking_code' => 'HF-20261128-B001',
            'user_id' => $this->userB->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::WaitingVerification,
            'expires_at' => now()->addDay(),
        ]);

        $proofPath = 'payments/secret_proof.jpg';
        Storage::disk('local')->put($proofPath, 'dummy image content');

        $paymentB = Payment::create([
            'order_id' => $orderB->id,
            'proof_path' => $proofPath,
            'status' => PaymentStatus::Pending,
        ]);

        // User A akses detail pesanan User B -> 403
        $resOrder = $this->actingAs($this->userA)->get("/orders/{$orderB->booking_code}");
        $resOrder->assertStatus(403);

        // User A akses bukti bayar User B -> 403
        $resPayment = $this->actingAs($this->userA)->get("/files/payment/{$paymentB->id}");
        $resPayment->assertStatus(403);
    }

    /**
     * Skenario 8: User biasa mendapat 403 di /admin.
     */
    public function test_scenario_8_user_biasa_mendapat_403_di_admin(): void
    {
        $response = $this->actingAs($this->userA)->get('/admin');
        $response->assertStatus(403);

        $responseOrders = $this->actingAs($this->userA)->get('/admin/orders');
        $responseOrders->assertStatus(403);
    }

    /**
     * Skenario 9: Admin approve: status paid dan jumlah tiket = quantity * people_per_ticket.
     */
    public function test_scenario_9_admin_approve_status_paid_dan_jumlah_tiket_sama_dengan_quantity_kali_people_per_ticket(): void
    {
        // groupTicket: people_per_ticket = 3, quantity = 2 -> total tiket harus 6
        $order = Order::create([
            'booking_code' => 'HF-20261128-APPR',
            'user_id' => $this->userA->id,
            'ticket_type_id' => $this->groupTicket->id,
            'quantity' => 2,
            'total_price' => 200000,
            'status' => OrderStatus::WaitingVerification,
            'expires_at' => now()->addDay(),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => 'payments/dummy.jpg',
            'status' => PaymentStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/payments/{$payment->id}/approve");
        $response->assertRedirect(route('admin.payments.index'));

        $this->assertEquals(OrderStatus::Paid, $order->fresh()->status);
        $this->assertEquals(PaymentStatus::Approved, $payment->fresh()->status);
        $this->assertEquals($this->admin->id, $payment->fresh()->verified_by);

        // Harus ada 2 * 3 = 6 tiket
        $this->assertEquals(6, Ticket::where('order_id', $order->id)->count());
    }

    /**
     * Skenario 10: Admin reject tanpa alasan: validasi gagal.
     */
    public function test_scenario_10_admin_reject_tanpa_alasan_validasi_gagal(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261128-RJ01',
            'user_id' => $this->userA->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::WaitingVerification,
            'expires_at' => now()->addDay(),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'proof_path' => 'payments/dummy.jpg',
            'status' => PaymentStatus::Pending,
        ]);

        // Kirim penolakan tanpa reject_reason
        $response = $this->actingAs($this->admin)->post("/admin/payments/{$payment->id}/reject", [
            'reject_reason' => '',
        ]);

        $response->assertSessionHasErrors(['reject_reason']);
        $this->assertEquals(PaymentStatus::Pending, $payment->fresh()->status);
    }

    /**
     * Skenario 11: Check-in tiket valid: sukses. Check-in kedua kali: ditolak (used). Kode palsu: invalid.
     */
    public function test_scenario_11_checkin_tiket_valid_sukses_checkin_kedua_kali_ditolak_used_kode_palsu_invalid(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261128-CKIN',
            'user_id' => $this->userA->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addDay(),
        ]);

        $ticket = Ticket::create([
            'order_id' => $order->id,
            'ticket_code' => 'HF-VALIDCHECKIN123',
            'is_used' => false,
        ]);

        // 1. Check-in tiket valid -> sukses
        $resValid = $this->actingAs($this->admin)->postJson('/admin/checkin', [
            'ticket_code' => 'HF-VALIDCHECKIN123',
        ]);
        $resValid->assertStatus(200)
            ->assertJson([
                'result' => 'success',
            ]);
        $this->assertTrue($ticket->fresh()->is_used);
        $this->assertNotNull($ticket->fresh()->used_at);

        // 2. Check-in kedua kali -> ditolak (used)
        $resSecond = $this->actingAs($this->admin)->postJson('/admin/checkin', [
            'ticket_code' => 'HF-VALIDCHECKIN123',
        ]);
        $resSecond->assertStatus(200)
            ->assertJson([
                'result' => 'used',
            ]);

        // 3. Kode palsu -> invalid
        $resFake = $this->actingAs($this->admin)->postJson('/admin/checkin', [
            'ticket_code' => 'HF-KODEPALSU999999',
        ]);
        $resFake->assertStatus(200)
            ->assertJson([
                'result' => 'invalid',
            ]);
    }

    /**
     * Skenario 12: Command app:expire-orders mengubah pesanan lewat batas menjadi expired dan mengembalikan kuota.
     */
    public function test_scenario_12_command_app_expire_orders_mengubah_pesanan_lewat_batas_menjadi_expired_dan_mengembalikan_kuota(): void
    {
        // singleTicket: kuota 10
        $this->assertEquals(10, $this->singleTicket->fresh()->remaining_quota);

        // Buat pesanan pending yang sudah lewat deadline
        $expiredOrder = Order::create([
            'booking_code' => 'HF-20261128-EXPR',
            'user_id' => $this->userA->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 3,
            'total_price' => 120000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->subMinutes(15), // sudah lewat
        ]);

        // Sebelum command berjalan: kuota tersisa 7
        $this->assertEquals(7, $this->singleTicket->fresh()->remaining_quota);

        // Jalankan artisan command app:expire-orders
        $exitCode = Artisan::call('app:expire-orders');
        $this->assertEquals(0, $exitCode);

        // Status pesanan menjadi expired
        $this->assertEquals(OrderStatus::Expired, $expiredOrder->fresh()->status);

        // Kuota tiket kembali penuh menjadi 10!
        $this->assertEquals(10, $this->singleTicket->fresh()->remaining_quota);
    }
}
