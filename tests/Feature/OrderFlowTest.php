<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Event $event;
    private TicketType $regularTicket;
    private TicketType $civitasTicket;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create([
            'email' => 'user1@harfest.test',
            'role' => UserRole::User,
        ]);

        $this->otherUser = User::factory()->create([
            'email' => 'user2@harfest.test',
            'role' => UserRole::User,
        ]);

        $this->event = Event::create([
            'name' => 'Harfest 2026',
            'description' => 'Test event',
            'venue' => 'Lapangan Utama',
            'event_date' => now()->addMonth(),
            'status' => EventStatus::OnSale,
        ]);

        $this->regularTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Tiket Umum 1 Orang',
            'description' => 'Untuk umum',
            'price' => 40000,
            'quota' => 10,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $this->civitasTicket = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Tiket Civitas',
            'description' => 'Khusus mahasiswa',
            'price' => 30000,
            'quota' => 5,
            'people_per_ticket' => 1,
            'requires_student_id' => true,
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_orders_page(): void
    {
        $response = $this->get('/orders');
        $response->assertRedirect('/login');

        $response = $this->get('/orders/create/' . $this->regularTicket->id);
        $response->assertRedirect('/login');
    }

    public function test_guest_can_view_tickets_page(): void
    {
        $response = $this->get('/tickets');
        $response->assertStatus(200);
        $response->assertSee('Tiket Umum 1 Orang');
        $response->assertSee('Tiket Civitas');
    }

    public function test_user_can_create_regular_order(): void
    {
        $response = $this->actingAs($this->user)->post('/orders', [
            'ticket_type_id' => $this->regularTicket->id,
            'quantity' => 2,
        ]);

        $order = Order::where('user_id', $this->user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(80000, $order->total_price);
        $this->assertEquals(OrderStatus::PendingPayment, $order->status);
        $this->assertMatchesRegularExpression('/^HF\d+-/', $order->booking_code);

        $response->assertRedirect('/orders/' . $order->booking_code);
    }

    public function test_civitas_order_requires_student_id_and_ktm(): void
    {
        $response = $this->actingAs($this->user)->post('/orders', [
            'ticket_type_id' => $this->civitasTicket->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors(['student_id', 'student_card']);
        $this->assertEquals(0, Order::count());
    }

    public function test_user_can_create_civitas_order_with_valid_data(): void
    {
        $ktmFile = UploadedFile::fake()->image('ktm.jpg', 600, 400);

        $response = $this->actingAs($this->user)->post('/orders', [
            'ticket_type_id' => $this->civitasTicket->id,
            'quantity' => 1,
            'student_id' => '12345678',
            'student_card' => $ktmFile,
        ]);

        $order = Order::where('user_id', $this->user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('12345678', $order->student_id);
        $this->assertNotNull($order->student_card_path);
        Storage::disk('local')->assertExists($order->student_card_path);

        $response->assertRedirect('/orders/' . $order->booking_code);
    }

    public function test_cannot_order_more_than_remaining_quota(): void
    {
        // Kuota civitas = 5, request = 6
        $ktmFile = UploadedFile::fake()->image('ktm.jpg');

        $response = $this->actingAs($this->user)->post('/orders', [
            'ticket_type_id' => $this->civitasTicket->id,
            'quantity' => 6,
            'student_id' => '12345678',
            'student_card' => $ktmFile,
        ]);

        $response->assertSessionHasErrors(['quantity']);
        $this->assertEquals(0, Order::count());
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261128-TEST',
            'user_id' => $this->otherUser->id,
            'ticket_type_id' => $this->regularTicket->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->user)->get('/orders/' . $order->booking_code);
        $response->assertStatus(403);
    }

    public function test_user_can_cancel_pending_order_and_quota_is_restored(): void
    {
        $order = Order::create([
            'booking_code' => 'HF-20261128-CNCL',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->regularTicket->id,
            'quantity' => 3,
            'total_price' => 120000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addDay(),
        ]);

        $this->assertEquals(7, $this->regularTicket->fresh()->remaining_quota);

        $response = $this->actingAs($this->user)->post('/orders/' . $order->booking_code . '/cancel');
        $response->assertRedirect('/orders/' . $order->booking_code);

        $this->assertEquals(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertEquals(10, $this->regularTicket->fresh()->remaining_quota);
    }
}
