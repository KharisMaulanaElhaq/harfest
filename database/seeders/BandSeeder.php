<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Event;
use Illuminate\Database\Seeder;

class BandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vol1 = Event::where('slug', 'vol-1')->first();
        $vol2 = Event::where('slug', 'vol-2')->first();

        // Bands for Vol 1 (Arsip)
        if ($vol1) {
            $vol1Bands = [
                [
                    'name' => 'Enau',
                    'description' => 'Penyanyi dan penulis lagu folk-indie dengan lirik puitis reflektif yang sukses menyihir ribuan penonton di panggung utama Harfest Vol 1.',
                    'photo' => null,
                    'is_headliner' => true,
                    'logo' => null,
                    'instagram_url' => 'https://instagram.com/enau_music',
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Artura',
                    'description' => 'Kuartet indie rock berenergi dinamis yang membuka keseruan panggung festival musik kampus.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Kalarupa',
                    'description' => 'Grup alternative-pop psikedelik yang memadukan visual puitis dengan melodi syahdu khas senja kampus.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Matraman',
                    'description' => 'Kuintet jazz-funk fusi berenergi tinggi yang siap mengguncang dan mengajak seisi panggung berdansa.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 4,
                ],
                [
                    'name' => 'Substring',
                    'description' => 'Kolektif midwest-emo dan post-rock dengan distorsi gitar raw dan lirik penuh luapan emosi anak muda.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 5,
                ],
                [
                    'name' => 'Algorhythm',
                    'description' => 'Band indie math-rock elektro kampus dengan eksplorasi ketukan poliritmik dinamis dan synthesizer bertenaga.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 6,
                ],
            ];

            foreach ($vol1Bands as $band) {
                Band::updateOrCreate(
                    [
                        'event_id' => $vol1->id,
                        'name' => $band['name'],
                    ],
                    $band
                );
            }
        }

        // Bands for Vol 2 (Aktif - Headliner Akan Diumumkan)
        if ($vol2) {
            $vol2Bands = [
                [
                    'name' => 'Algorhythm',
                    'description' => 'Band indie math-rock elektro kampus dengan eksplorasi ketukan poliritmik dinamis dan synthesizer bertenaga.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Kalarupa',
                    'description' => 'Grup alternative-pop psikedelik yang memadukan visual puitis dengan melodi syahdu khas senja kampus.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Substring',
                    'description' => 'Kolektif midwest-emo dan post-rock dengan distorsi gitar raw dan lirik penuh luapan emosi anak muda.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 3,
                ],
                [
                    'name' => 'Matraman',
                    'description' => 'Kuintet jazz-funk fusi berenergi tinggi yang siap mengguncang dan mengajak seisi panggung berdansa.',
                    'photo' => null,
                    'is_headliner' => false,
                    'logo' => null,
                    'instagram_url' => null,
                    'sort_order' => 4,
                ],
            ];

            foreach ($vol2Bands as $band) {
                Band::updateOrCreate(
                    [
                        'event_id' => $vol2->id,
                        'name' => $band['name'],
                    ],
                    $band
                );
            }
        }
    }
}
