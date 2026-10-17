<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Services\CheckinService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAndCheckinTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected User $admin;
    protected Event $event;
    protected TicketType $singleTicket;
    protected TicketType $groupTicket;
    protected TicketService $ticketService;
    protected CheckinService $checkinService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ticketService = app(TicketService::class);
        $this->checkinService = app(CheckinService::class);

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

        $this->event = Event::create([
            'name' => 'Harfest 2026',
            'description' => 'Konser akbar tahunan kampus',
            'venue' => 'Lapangan Utama Kampus Harkat Negeri',
            'event_date' => now()->addDays(30),
            'is_active' => true,
        ]);

        $this->singleTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Umum 1 Orang',
            'description' => 'Untuk 1 orang',
            'price' => 40000,
            'quota' => 100,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $this->groupTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Umum 3 Orang',
            'description' => 'Paket hemat 3 orang',
            'price' => 100000,
            'quota' => 50,
            'people_per_ticket' => 3,
            'requires_student_id' => false,
            'is_active' => true,
        ]);
    }

    public function test_ticket_service_issues_correct_number_of_tickets_and_is_idempotent(): void
    {
        // Beli tiket Umum 3 Orang x 2 paket -> harus terbit 6 tiket
        $order = Order::create([
            'booking_code' => 'HF-20261017-GRP2',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->groupTicket->id,
            'quantity' => 2,
            'total_price' => 200000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $tickets = $this->ticketService->issue($order);

        $this->assertCount(6, $tickets);
        $this->assertEquals(6, $order->tickets()->count());

        foreach ($tickets as $ticket) {
            $this->assertStringStartsWith('HF-', $ticket->ticket_code);
            $this->assertEquals(19, strlen($ticket->ticket_code)); // HF- + 16 chars
            $this->assertFalse($ticket->is_used);
            $this->assertNull($ticket->used_at);
        }

        // Test idempotency: memanggil ulang issue() tidak boleh menambah record baru
        $secondCallTickets = $this->ticketService->issue($order);
        $this->assertCount(6, $secondCallTickets);
        $this->assertEquals(6, $order->tickets()->count());
    }

    public function test_user_cannot_access_e_tickets_if_order_not_paid(): void
    {
        $unpaidOrder = Order::create([
            'booking_code' => 'HF-20261017-UNPD',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.tickets', $unpaidOrder->booking_code));

        $response->assertRedirect(route('orders.show', $unpaidOrder->booking_code));
        $response->assertSessionHas('error');
    }

    public function test_user_can_view_e_tickets_when_order_paid(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261017-PAID',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->groupTicket->id,
            'quantity' => 2,
            'total_price' => 200000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $this->ticketService->issue($order);

        $response = $this->actingAs($this->user)->get(route('orders.tickets', $order->booking_code));

        $response->assertOk();
        $response->assertSee('E-Tiket Resmi Harfest 2026');
        $response->assertSee($order->booking_code);
        $response->assertSee('Umum 3 Orang');
        $response->assertSee('Cetak / Simpan PDF');

        // Other user cannot view (403)
        $this->actingAs($this->otherUser)
            ->get(route('orders.tickets', $order->booking_code))
            ->assertForbidden();
    }

    public function test_checkin_flow_success_duplicate_and_invalid(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261017-CHCK',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $tickets = $this->ticketService->issue($order);
        $ticket = $tickets->first();

        // 1. Check-in pertama kali -> SUKSES
        $result = $this->checkinService->verify($ticket->ticket_code, $this->admin);

        $this->assertEquals('success', $result['result']);
        $this->assertStringContainsString('Check-in berhasil', $result['message']);

        $ticket->refresh();
        $this->assertTrue($ticket->is_used);
        $this->assertNotNull($ticket->used_at);
        $this->assertEquals($this->admin->id, $ticket->checked_by);

        // 2. Check-in ulang tiket yang sama -> DITOLAK (USED)
        $duplicateResult = $this->checkinService->verify($ticket->ticket_code, $this->admin);

        $this->assertEquals('used', $duplicateResult['result']);
        $this->assertStringContainsString('Tiket sudah pernah digunakan', $duplicateResult['message']);

        // 3. Check-in kode palsu / tidak terdaftar -> DITOLAK (INVALID)
        $fakeResult = $this->checkinService->verify('HF-PALSU1234567890', $this->admin);

        $this->assertEquals('invalid', $fakeResult['result']);
        $this->assertStringContainsString('tidak ditemukan', $fakeResult['message']);
    }

    public function test_admin_checkin_controller_endpoints_and_authorization(): void
    {
        // 1. Non-admin forbidden
        $this->actingAs($this->user)
            ->get(route('admin.checkin.index'))
            ->assertForbidden();

        // 2. Admin can access check-in page
        $response = $this->actingAs($this->admin)->get(route('admin.checkin.index'));
        $response->assertOk();
        $response->assertSee('Gate Check-in Scanner');

        // 3. Admin verify via JSON request
        $order = Order::create([
            'booking_code' => 'HF-20261017-API',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->singleTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);
        $ticket = $this->ticketService->issue($order)->first();

        $postResponse = $this->actingAs($this->admin)->postJson(route('admin.checkin.verify'), [
            'ticket_code' => $ticket->ticket_code,
        ]);

        $postResponse->assertOk();
        $postResponse->assertJson([
            'result' => 'success',
        ]);
    }
}
