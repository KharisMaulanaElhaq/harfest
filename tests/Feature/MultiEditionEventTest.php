<?php

namespace Tests\Feature;

use App\Console\Commands\CloseFinishedSales;
use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Band;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Services\CheckinService;
use App\Services\EventService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MultiEditionEventTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $admin;
    private Event $vol1;
    private Event $vol2;
    private TicketType $vol1Ticket;
    private TicketType $vol2Ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'name' => 'Calon Penonton',
            'email' => 'penonton@harfest.test',
            'phone' => '081234567890',
            'role' => UserRole::User,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Administrator Harfest',
            'email' => 'admin@harfest.test',
            'phone' => '08111222333',
            'role' => UserRole::Admin,
        ]);

        // Vol 1: Finished Archive Edition
        $this->vol1 = Event::create([
            'name' => 'Harfest Vol. 1',
            'edition' => 1,
            'slug' => 'vol-1',
            'tagline' => 'Gemuruh Perdana',
            'description' => 'Arsip Harfest edisi pertama bersama Enau',
            'venue' => 'Lapangan Utama Kampus Harkat Negeri',
            'event_date' => now()->subMonths(6),
            'status' => EventStatus::Finished,
            'sales_open_at' => now()->subMonths(8),
            'sales_close_at' => now()->subMonths(6),
        ]);

        $this->vol1Ticket = TicketType::create([
            'event_id' => $this->vol1->id,
            'name' => 'Presale Vol 1',
            'description' => 'Tiket arsip',
            'price' => 35000,
            'quota' => 50,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        // Vol 2: Active On-Sale Edition
        $this->vol2 = Event::create([
            'name' => 'Harfest Vol. 2',
            'edition' => 2,
            'slug' => 'vol-2',
            'tagline' => 'Mari Ciptakan Gemuruh Bersama!',
            'description' => 'Edisi aktif Harfest 2026',
            'venue' => 'Lapangan Utama Kampus Harkat Negeri',
            'event_date' => now()->addMonths(2),
            'status' => EventStatus::OnSale,
            'sales_open_at' => now()->subDays(5),
            'sales_close_at' => now()->addMonths(2),
        ]);

        $this->vol2Ticket = TicketType::create([
            'event_id' => $this->vol2->id,
            'name' => 'Umum 1 Orang Vol 2',
            'description' => 'Tiket reguler aktif',
            'price' => 40000,
            'quota' => 100,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Skenario 13: Pesanan untuk event finished atau draft ditolak.
     */
    public function test_scenario_13_pesanan_untuk_event_finished_atau_draft_ditolak(): void
    {
        $orderService = app(OrderService::class);

        // 1. Coba pesan tiket untuk event Vol 1 (status: Finished) via OrderService
        $this->expectException(ValidationException::class);
        $orderService->create($this->buyer, $this->vol1Ticket, 2);

        // Pastikan tidak ada order yang terbuat
        $this->assertEquals(0, Order::where('event_id', $this->vol1->id)->count());
    }

    /**
     * Skenario 13b: Pesanan via HTTP POST untuk event draft ditolak.
     */
    public function test_scenario_13b_pesanan_http_untuk_event_draft_ditolak(): void
    {
        $vol3Draft = Event::create([
            'name' => 'Harfest Vol. 3',
            'edition' => 3,
            'slug' => 'vol-3',
            'status' => EventStatus::Draft,
            'description' => 'Edisi persiapan',
            'venue' => 'Main Stage',
            'event_date' => now()->addYear(),
        ]);

        $vol3Ticket = TicketType::create([
            'event_id' => $vol3Draft->id,
            'name' => 'Early Bird Vol 3',
            'description' => 'Belum buka',
            'price' => 30000,
            'quota' => 20,
            'people_per_ticket' => 1,
            'requires_student_id' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->buyer)->post('/orders', [
            'ticket_type_id' => $vol3Ticket->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors(['event']);
        $this->assertEquals(0, Order::where('event_id', $vol3Draft->id)->count());
    }

    /**
     * Skenario 14: Kuota Vol 1 dan Vol 2 terpisah: pembelian di Vol 2 tidak mengurangi kuota Vol 1.
     */
    public function test_scenario_14_kuota_vol_1_dan_vol_2_terpisah(): void
    {
        $initialVol1Remaining = $this->vol1Ticket->remainingQuota();
        $initialVol2Remaining = $this->vol2Ticket->remainingQuota();

        $this->assertEquals(50, $initialVol1Remaining);
        $this->assertEquals(100, $initialVol2Remaining);

        // Pembelian 5 tiket di Vol 2
        $response = $this->actingAs($this->buyer)->post('/orders', [
            'ticket_type_id' => $this->vol2Ticket->id,
            'quantity' => 5,
        ]);
        $response->assertRedirect();

        // Kuota Vol 2 harus berkurang 5 (menjadi 95)
        $this->assertEquals(95, $this->vol2Ticket->fresh()->remainingQuota());

        // Kuota Vol 1 TIDAK BOLEH BERUBAH (tetap 50)
        $this->assertEquals(50, $this->vol1Ticket->fresh()->remainingQuota());
    }

    /**
     * Skenario 15: Check-in tiket Vol 1 pada event kerja Vol 2 menghasilkan wrong_event.
     */
    public function test_scenario_15_checkin_tiket_vol_1_pada_event_kerja_vol_2_menghasilkan_wrong_event(): void
    {
        // 1. Buat order lunas dan tiket sah untuk Vol 1
        $vol1Order = Order::create([
            'booking_code' => 'HF1-20260410-0001',
            'event_id' => $this->vol1->id,
            'user_id' => $this->buyer->id,
            'ticket_type_id' => $this->vol1Ticket->id,
            'quantity' => 1,
            'total_price' => 35000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->subMonths(5),
        ]);

        $vol1TicketCode = 'HF-VOL1TESTCODE99';
        $ticketVol1 = Ticket::create([
            'order_id' => $vol1Order->id,
            'ticket_code' => $vol1TicketCode,
            'holder_name' => $this->buyer->name,
            'is_used' => false,
        ]);

        // 2. Petugas scan tiket Vol 1 di gate pemeriksaan Vol 2
        $checkinService = app(CheckinService::class);
        $result = $checkinService->verify($vol1TicketCode, $this->admin, $this->vol2);

        $this->assertEquals('wrong_event', $result['result']);
        $this->assertStringContainsString($this->vol1->name, $result['message']);
        $this->assertStringContainsString($this->vol2->name, $result['message']);

        // Tiket tidak boleh berubah menjadi is_used
        $this->assertFalse($ticketVol1->fresh()->is_used);

        // 3. Verifikasi juga via HTTP endpoint checkin
        $response = $this->actingAs($this->admin)->postJson('/admin/checkin/verify', [
            'ticket_code' => $vol1TicketCode,
            'event_id' => $this->vol2->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('result', 'wrong_event')
            ->assertJsonPath('event_name', $this->vol1->name);
    }

    /**
     * Skenario 16: EventService::duplicate() menyalin jenis tiket tapi tidak menyalin pesanan dan band.
     */
    public function test_scenario_16_duplicate_menyalin_jenis_tiket_tanpa_pesanan_dan_band(): void
    {
        // Tambahkan band dan pesanan pada Vol 2
        Band::create([
            'event_id' => $this->vol2->id,
            'name' => 'Band Vol 2',
            'description' => 'Test band',
            'sort_order' => 1,
        ]);

        Order::create([
            'booking_code' => 'HF2-20261128-ORDER1',
            'event_id' => $this->vol2->id,
            'user_id' => $this->buyer->id,
            'ticket_type_id' => $this->vol2Ticket->id,
            'quantity' => 2,
            'total_price' => 80000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addDay(),
        ]);

        $eventService = app(EventService::class);
        $duplicatedEvent = $eventService->duplicate($this->vol2);

        // Verifikasi properti event baru
        $this->assertEquals(3, $duplicatedEvent->edition);
        $this->assertEquals('vol-3', $duplicatedEvent->slug);
        $this->assertEquals(EventStatus::Draft, $duplicatedEvent->status);
        $this->assertEquals('Harfest Vol 3', $duplicatedEvent->name);

        // Verifikasi jenis tiket disalin
        $this->assertEquals(1, $duplicatedEvent->ticketTypes()->count());
        $duplicatedTicketType = $duplicatedEvent->ticketTypes()->first();
        $this->assertEquals($this->vol2Ticket->name, $duplicatedTicketType->name);
        $this->assertEquals($this->vol2Ticket->price, $duplicatedTicketType->price);
        $this->assertEquals($this->vol2Ticket->quota, $duplicatedTicketType->quota);

        // Verifikasi BAND dan PESANAN TIDAK DISALIN
        $this->assertEquals(0, $duplicatedEvent->bands()->count());
        $this->assertEquals(0, $duplicatedEvent->orders()->count());
    }

    /**
     * Skenario 17: Admin yang mengganti event switcher melihat data dashboard dan ekspor CSV sesuai event terpilih.
     */
    public function test_scenario_17_admin_switcher_isolates_dashboard_and_export(): void
    {
        // Buat pesanan paid di Vol 1 (Rp 70.000)
        $orderVol1 = Order::create([
            'booking_code' => 'HF1-20260410-0010',
            'event_id' => $this->vol1->id,
            'user_id' => $this->buyer->id,
            'ticket_type_id' => $this->vol1Ticket->id,
            'quantity' => 2,
            'total_price' => 70000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->subMonths(5),
        ]);

        // Buat pesanan paid di Vol 2 (Rp 120.000)
        $orderVol2 = Order::create([
            'booking_code' => 'HF2-20261128-0020',
            'event_id' => $this->vol2->id,
            'user_id' => $this->buyer->id,
            'ticket_type_id' => $this->vol2Ticket->id,
            'quantity' => 3,
            'total_price' => 120000,
            'status' => OrderStatus::Paid,
            'expires_at' => now()->addDay(),
        ]);

        // 1. Admin switch ke Vol 1
        $this->actingAs($this->admin)
            ->withSession(['admin_event_id' => $this->vol1->id])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('70.000') // Pendapatan Vol 1
            ->assertDontSee('120.000') // Tidak boleh melihat pendapatan Vol 2
            ->assertSee('HF1-20260410-0010')
            ->assertDontSee('HF2-20261128-0020');

        // Ekspor CSV di Vol 1
        $csvVol1Response = $this->actingAs($this->admin)
            ->withSession(['admin_event_id' => $this->vol1->id])
            ->get(route('admin.orders.export'));

        $csvVol1Response->assertOk();
        $contentVol1 = $csvVol1Response->streamedContent();
        $this->assertStringContainsString('HF1-20260410-0010', $contentVol1);
        $this->assertStringNotContainsString('HF2-20261128-0020', $contentVol1);

        // 2. Admin switch ke Vol 2
        $this->actingAs($this->admin)
            ->withSession(['admin_event_id' => $this->vol2->id])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('120.000') // Pendapatan Vol 2
            ->assertDontSee('70.000') // Tidak boleh melihat pendapatan Vol 1
            ->assertSee('HF2-20261128-0020')
            ->assertDontSee('HF1-20260410-0010');

        // Ekspor CSV di Vol 2
        $csvVol2Response = $this->actingAs($this->admin)
            ->withSession(['admin_event_id' => $this->vol2->id])
            ->get(route('admin.orders.export'));

        $csvVol2Response->assertOk();
        $contentVol2 = $csvVol2Response->streamedContent();
        $this->assertStringContainsString('HF2-20261128-0020', $contentVol2);
        $this->assertStringNotContainsString('HF1-20260410-0010', $contentVol2);
    }

    /**
     * Skenario 18: /events/vol-1 tampil dengan badge Selesai dan tanpa tombol beli,
     * sedangkan /events/vol-2 tampil dengan tombol beli aktif.
     */
    public function test_scenario_18_public_views_display_correct_badges_and_actions(): void
    {
        // 1. Cek index /events
        $eventsIndexResponse = $this->get(route('events.index'));
        $eventsIndexResponse->assertOk()
            ->assertSee('Harfest Vol. 1')
            ->assertSee('Harfest Vol. 2')
            ->assertSee('Selesai')
            ->assertSee('Dijual');

        // 2. Cek detail Vol 1 (Finished)
        $vol1DetailResponse = $this->get(route('events.show', 'vol-1'));
        $vol1DetailResponse->assertOk()
            ->assertSee('Harfest Vol. 1')
            ->assertSee('Selesai')
            ->assertSee('Penjualan sudah ditutup')
            ->assertDontSee('Beli Tiket Sekarang');

        // 3. Cek detail Vol 2 (On Sale)
        $vol2DetailResponse = $this->get(route('events.show', 'vol-2'));
        $vol2DetailResponse->assertOk()
            ->assertSee('Harfest Vol. 2')
            ->assertSee('Dijual')
            ->assertSee('Beli Tiket Ini')
            ->assertSee(route('orders.create', $this->vol2Ticket->id));
    }

    /**
     * Bonus Skenario: Command app:close-finished-sales mengubah status event lewat batas menjadi Finished.
     */
    public function test_command_close_finished_sales(): void
    {
        $expiredSaleEvent = Event::create([
            'name' => 'Expired Sale Event',
            'edition' => 99,
            'slug' => 'vol-99',
            'status' => EventStatus::OnSale,
            'sales_close_at' => now()->subDay(),
            'event_date' => now()->addDays(5),
            'venue' => 'Hall A',
            'description' => 'Sales period has passed',
        ]);

        $this->artisan('app:close-finished-sales')
            ->expectsOutputToContain('has been updated to Finished')
            ->assertExitCode(0);

        $this->assertEquals(EventStatus::Finished, $expiredSaleEvent->fresh()->status);
    }
}
