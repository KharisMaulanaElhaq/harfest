<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Services\OrderService;
use App\Services\QrService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimezoneWibTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $otherUser;
    protected Event $event;
    protected TicketType $ticketType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $this->otherUser = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $this->event = Event::create([
            'edition' => 2,
            'slug' => 'vol-2',
            'name' => 'Harfest 2026 Vol 2',
            'description' => 'Konser musik kampus',
            'venue' => 'Lapangan Utama Kampus',
            'event_date' => Carbon::parse('2026-11-28 15:00:00', 'Asia/Jakarta'),
            'status' => \App\Enums\EventStatus::OnSale,
        ]);

        $this->ticketType = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Umum 3 Orang',
            'description' => 'Paket 3 orang',
            'price' => 100000,
            'quota' => 50,
            'people_per_ticket' => 3,
            'requires_student_id' => false,
            'is_active' => true,
        ]);
    }

    public function test_pesanan_dibuat_jam_14_00_wib_tampil_14_00_wib_dan_expires_at_plus_24_jam(): void
    {
        // Freeze time at 14:00 WIB (Asia/Jakarta)
        $knownDate = Carbon::parse('2026-11-02 14:00:00', 'Asia/Jakarta');
        Carbon::setTestNow($knownDate);

        $orderService = app(OrderService::class);
        $order = $orderService->create($this->user, $this->ticketType, 1);

        // 1. Assert created_at has time 14:00 WIB
        $this->assertEquals('14:00', $order->created_at->timezone('Asia/Jakarta')->format('H:i'));

        // 2. Assert expires_at is exactly +24 hours (14:00 the next day)
        $expectedExpiry = Carbon::parse('2026-11-03 14:00:00', 'Asia/Jakarta');
        $this->assertEquals($expectedExpiry->toIso8601String(), $order->expires_at->toIso8601String());

        // 3. Assert detail page displays "14:00 WIB" and expires_at with WIB
        $response = $this->actingAs($this->user)
            ->get(route('orders.show', $order->booking_code));

        $response->assertOk();
        $response->assertSee('14:00 WIB');

        Carbon::setTestNow(); // unfreeze
    }

    public function test_format_wib_helper_formats_correctly(): void
    {
        $date = Carbon::parse('2026-11-02 14:00:00', 'Asia/Jakarta');
        $formatted = format_wib($date);

        $this->assertEquals('02 November 2026, 14:00 WIB', $formatted);
    }

    public function test_qr_service_returns_data_uri_png(): void
    {
        $qrService = app(QrService::class);
        $dataUri = $qrService->dataUri('HF2-20261102-TEST-CODE', 'png', 180);

        $this->assertNotNull($dataUri);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }

    public function test_qr_service_handles_empty_code_safely(): void
    {
        $qrService = app(QrService::class);
        $dataUri = $qrService->dataUri('');

        $this->assertNull($dataUri);
    }

    public function test_user_can_download_all_tickets_pdf_for_paid_order(): void
    {
        $order = Order::create([
            'booking_code' => 'HF2-20261102-PDFALL',
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 100000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        app(TicketService::class)->issue($order);

        $response = $this->actingAs($this->user)
            ->get(route('orders.tickets.pdf', $order->booking_code));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('e-tiket-HF2-20261102-PDFALL.pdf', $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_user_can_download_single_ticket_pdf_for_paid_order(): void
    {
        $order = Order::create([
            'booking_code' => 'HF2-20261102-PDFONE',
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 100000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $tickets = app(TicketService::class)->issue($order);
        $singleTicket = $tickets->first();

        $response = $this->actingAs($this->user)
            ->get(route('orders.tickets.single.pdf', [
                'order' => $order->booking_code,
                'ticket' => $singleTicket->ticket_code,
            ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString("e-tiket-{$singleTicket->ticket_code}.pdf", $response->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_unpaid_order_cannot_download_tickets_pdf(): void
    {
        $order = Order::create([
            'booking_code' => 'HF2-20261102-UNPAID',
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 100000,
            'status' => OrderStatus::PendingPayment,
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('orders.tickets.pdf', $order->booking_code));

        $response->assertRedirect(route('orders.show', $order->booking_code));
    }

    public function test_other_user_cannot_access_or_download_tickets_pdf(): void
    {
        $order = Order::create([
            'booking_code' => 'HF2-20261102-FORBID',
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 100000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->otherUser)
            ->get(route('orders.tickets.pdf', $order->booking_code));

        $response->assertForbidden();
    }
}

