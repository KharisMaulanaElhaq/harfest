<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vol 1 (Arsip / Finished)
        Event::updateOrCreate(
            ['slug' => 'vol-1'],
            [
                'name' => 'Harfest Vol 1',
                'edition' => 1,
                'tagline' => 'Pesta Musik Kampus Pertama',
                'description' => 'Edisi perdana festival musik kampus Universitas Harkat Negeri yang sukses mengguncang panggung dengan Enau dan talenta kampus terbaik.',
                'venue' => 'Lapangan Utama Universitas Harkat Negeri',
                'event_date' => Carbon::parse('2025-11-29 16:00:00'),
                'poster' => null,
                'logo' => null,
                'banner' => null,
                'status' => EventStatus::Finished,
                'sales_open_at' => Carbon::parse('2025-10-01 00:00:00'),
                'sales_close_at' => Carbon::parse('2025-11-28 23:59:59'),
            ]
        );

        // Vol 2 (Aktif / On Sale)
        Event::updateOrCreate(
            ['slug' => 'vol-2'],
            [
                'name' => 'Harfest 2026 (Vol 2)',
                'edition' => 2,
                'tagline' => 'Mari Ciptakan Gemuruh Bersama!',
                'description' => 'Festival musik tahunan terbesar persembahan Universitas Harkat Negeri in collaboration with Jendela Creative Corps! Rayakan keberagaman karya dan energi anak muda.',
                'venue' => 'Lapangan Utama Universitas Harkat Negeri',
                'event_date' => Carbon::parse('2026-11-28 15:00:00'),
                'poster' => null,
                'logo' => null,
                'banner' => null,
                'status' => EventStatus::OnSale,
                'sales_open_at' => Carbon::parse('2026-08-01 00:00:00'),
                'sales_close_at' => Carbon::parse('2026-11-27 23:59:59'),
            ]
        );
    }
}
