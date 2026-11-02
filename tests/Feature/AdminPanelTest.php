<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Band;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Event $event;
    protected TicketType $ticketType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'phone' => '08111222333',
        ]);

        $this->user = User::factory()->create([
            'role' => UserRole::User,
            'phone' => '081234567890',
        ]);

        $this->event = Event::create([
            'name' => 'Harfest 2026',
            'description' => 'Festival Musik Tahunan',
            'venue' => 'Lapangan Utama',
            'event_date' => now()->addDays(30),
            'status' => EventStatus::OnSale,
        ]);

        $this->ticketType = TicketType::create([
            'event_id' => $this->event->id,
            'name' => 'Umum 1 Orang',
            'description' => 'Tiket untuk 1 orang',
            'price' => 40000,
            'quota' => 50,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);
    }

    public function test_dashboard_metrics_and_authorization(): void
    {
        // Non-admin cannot access
        $this->actingAs($this->user)->get(route('admin.dashboard'))->assertForbidden();

        // Create paid order
        Order::create([
            'booking_code' => 'HF-DASH-PAID',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 2,
            'total_price' => 80000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        // Create waiting verification order
        Order::create([
            'booking_code' => 'HF-DASH-WAIT',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 1,
            'total_price' => 40000,
            'status' => OrderStatus::WaitingVerification,
            'expires_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Rp80.000'); // Total revenue from paid order
        $response->assertSee('2'); // Total tickets sold
        $response->assertSee('HF-DASH-PAID');
    }

    public function test_event_crud(): void
    {
        // 1. Index
        $this->actingAs($this->admin)->get(route('admin.events.index'))->assertOk()->assertSee('Harfest 2026');

        // 2. Create / Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.events.store'), [
            'name' => 'Harfest 2027 Special Edition',
            'venue' => 'Stadion Kampus Baru',
            'event_date' => now()->addYear()->format('Y-m-d H:i:s'),
            'description' => 'Event tahun depan',
            'is_active' => 1,
        ]);
        $storeResponse->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['name' => 'Harfest 2027 Special Edition']);

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.events.update', $this->event->id), [
            'name' => 'Harfest 2026 (Updated Venue)',
            'venue' => 'Gedung Serbaguna',
            'event_date' => now()->addDays(35)->format('Y-m-d H:i:s'),
            'description' => 'Deskripsi baru',
            'is_active' => 1,
        ]);
        $updateResponse->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['name' => 'Harfest 2026 (Updated Venue)', 'venue' => 'Gedung Serbaguna']);
    }

    public function test_band_crud(): void
    {
        // 1. Store
        $response = $this->actingAs($this->admin)->post(route('admin.bands.store'), [
            'event_id' => $this->event->id,
            'name' => 'The Antigravities',
            'description' => 'Indie rock campus',
            'sort_order' => 1,
        ]);
        $response->assertRedirect(route('admin.bands.index'));
        $this->assertDatabaseHas('bands', ['name' => 'The Antigravities', 'sort_order' => 1]);

        $band = Band::where('name', 'The Antigravities')->first();

        // 2. Update
        $this->actingAs($this->admin)->put(route('admin.bands.update', $band->id), [
            'event_id' => $this->event->id,
            'name' => 'The Antigravities (Headline)',
            'description' => 'Indie rock updated',
            'sort_order' => 99,
        ]);
        $this->assertDatabaseHas('bands', ['name' => 'The Antigravities (Headline)', 'sort_order' => 99]);

        // 3. Destroy
        $this->actingAs($this->admin)->delete(route('admin.bands.destroy', $band->id));
        $this->assertDatabaseMissing('bands', ['id' => $band->id]);
    }

    public function test_ticket_type_crud_and_quota_cannot_be_below_sold(): void
    {
        // 1. Place 10 orders committed
        Order::create([
            'booking_code' => 'HF-TT-COMMITTED',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 10,
            'total_price' => 400000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addHours(24),
        ]);

        $this->assertEquals(10, $this->ticketType->committedQuota());

        // 2. Try to update quota below committed quota (e.g. 5) -> Validation Error!
        $response = $this->actingAs($this->admin)->put(route('admin.ticket-types.update', $this->ticketType->id), [
            'event_id' => $this->event->id,
            'name' => $this->ticketType->name,
            'description' => $this->ticketType->description,
            'price' => 45000,
            'quota' => 5, // Kurang dari 10!
            'people_per_ticket' => 1,
        ]);

        $response->assertSessionHasErrors(['quota']);

        // 3. Update quota with valid number >= committed (e.g. 15) -> SUCCESS
        $validResponse = $this->actingAs($this->admin)->put(route('admin.ticket-types.update', $this->ticketType->id), [
            'event_id' => $this->event->id,
            'name' => 'Umum 1 Orang (Revisi)',
            'description' => $this->ticketType->description,
            'price' => 45000,
            'quota' => 15,
            'people_per_ticket' => 1,
            'is_active' => 1,
        ]);

        $validResponse->assertRedirect(route('admin.ticket-types.index'));
        $this->assertDatabaseHas('ticket_types', [
            'id' => $this->ticketType->id,
            'name' => 'Umum 1 Orang (Revisi)',
            'price' => 45000,
            'quota' => 15,
        ]);
    }

    public function test_admin_orders_list_and_csv_export(): void
    {
        Order::create([
            'booking_code' => 'HF-CSV-TEST1',
            'user_id' => $this->user->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 2,
            'total_price' => 80000,
            'status' => OrderStatus::Paid,
            'student_id' => '12345678',
            'expires_at' => now()->addHours(24),
        ]);

        // 1. Order Index
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('HF-CSV-TEST1');
        $indexResponse->assertSee('Ekspor CSV (Excel)');

        // 2. Order Export CSV Streamed
        $exportResponse = $this->actingAs($this->admin)->get(route('admin.orders.export'));
        $exportResponse->assertOk();
        $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $exportResponse->streamedContent();
        
        // Assert UTF-8 BOM present
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        
        // Assert CSV headers present
        $this->assertStringContainsString('Kode Booking', $content);
        $this->assertStringContainsString('Nama Pembeli', $content);
        $this->assertStringContainsString('HF-CSV-TEST1', $content);
        $this->assertStringContainsString('12345678', $content);
    }

    public function test_landing_page_reflects_admin_updates_dynamically(): void
    {
        // 1. Update event venue
        $this->event->update([
            'name' => 'HARFEST REVOLUTION 2026',
            'venue' => 'Amfiteater Kampus Pusat',
        ]);
        app(\App\Services\EventService::class)->clearCache();

        // 2. Update ticket price
        $this->ticketType->update([
            'price' => 55000,
        ]);

        // 3. Add a new band
        Band::create([
            'event_id' => $this->event->id,
            'name' => 'The Dynamic Echoes',
            'description' => 'Live band indie',
            'sort_order' => 1,
        ]);

        // 4. Visit landing page
        $landingResponse = $this->get(route('home'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Amfiteater Kampus Pusat');
        $landingResponse->assertSee('55.000');
        $landingResponse->assertSee('The Dynamic Echoes');
    }

    public function test_staff_role_permissions_and_restrictions(): void
    {
        $staff = User::factory()->create([
            'role' => UserRole::Staff,
            'phone' => '081299887766',
        ]);

        // Staff can access Dashboard
        $this->actingAs($staff)->get(route('admin.dashboard'))->assertOk();

        // Staff can access Orders
        $this->actingAs($staff)->get(route('admin.orders.index'))->assertOk();

        // Staff can access Check-in Scanner
        $this->actingAs($staff)->get(route('admin.checkin.index'))->assertOk();

        // Staff can access Event Switcher
        $this->actingAs($staff)->post(route('admin.events.switch', $this->event))->assertRedirect();

        // Staff MUST NOT access Events (403)
        $this->actingAs($staff)->get(route('admin.events.index'))->assertForbidden();

        // Staff MUST NOT access Ticket Types (403)
        $this->actingAs($staff)->get(route('admin.ticket-types.index'))->assertForbidden();

        // Staff MUST NOT access Bands (403)
        $this->actingAs($staff)->get(route('admin.bands.index'))->assertForbidden();

        // Staff MUST NOT access Payments (403)
        $this->actingAs($staff)->get(route('admin.payments.index'))->assertForbidden();
    }
}
