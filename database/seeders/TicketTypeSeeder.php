<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vol1 = Event::where('slug', 'vol-1')->first();
        $vol2 = Event::where('slug', 'vol-2')->first();

        // Ticket Types for Vol 1 (Arsip)
        if ($vol1) {
            $vol1Tickets = [
                [
                    'name' => 'Tiket Civitas Vol 1',
                    'description' => 'Tiket khusus civitas akademika Universitas Harkat Negeri edisi Vol 1.',
                    'price' => 25000,
                    'quota' => 200,
                    'people_per_ticket' => 1,
                    'requires_student_id' => true,
                    'is_active' => true,
                ],
                [
                    'name' => 'Tiket Umum Vol 1',
                    'description' => 'Tiket umum untuk 1 orang pengunjung edisi Vol 1.',
                    'price' => 35000,
                    'quota' => 100,
                    'people_per_ticket' => 1,
                    'requires_student_id' => false,
                    'is_active' => true,
                ],
            ];

            foreach ($vol1Tickets as $ticket) {
                TicketType::updateOrCreate(
                    [
                        'event_id' => $vol1->id,
                        'name' => $ticket['name'],
                    ],
                    $ticket
                );
            }
        }

        // Ticket Types for Vol 2 (Aktif)
        if ($vol2) {
            $vol2Tickets = [
                [
                    'name' => 'Tiket Civitas Harkat Negeri',
                    'description' => 'Khusus mahasiswa, dosen, alumni, dan karyawan Universitas Harkat Negeri. Wajib menyertakan NIM/ID serta foto KTM/identitas resmi.',
                    'price' => 30000,
                    'quota' => 300,
                    'people_per_ticket' => 1,
                    'requires_student_id' => true,
                    'is_active' => true,
                ],
                [
                    'name' => 'Tiket Umum 1 Orang',
                    'description' => 'Tiket masuk kategori umum berlaku untuk 1 (satu) orang pengunjung.',
                    'price' => 40000,
                    'quota' => 150,
                    'people_per_ticket' => 1,
                    'requires_student_id' => false,
                    'is_active' => true,
                ],
                [
                    'name' => 'Tiket Umum 3 Orang',
                    'description' => 'Paket bundling hemat yang berlaku untuk 3 (tiga) orang pengunjung sekaligus.',
                    'price' => 100000,
                    'quota' => 50,
                    'people_per_ticket' => 3,
                    'requires_student_id' => false,
                    'is_active' => true,
                ],
            ];

            foreach ($vol2Tickets as $ticket) {
                TicketType::updateOrCreate(
                    [
                        'event_id' => $vol2->id,
                        'name' => $ticket['name'],
                    ],
                    $ticket
                );
            }
        }
    }
}
