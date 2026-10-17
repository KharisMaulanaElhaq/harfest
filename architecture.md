# Architecture: Harfest Ticketing System

> Pasangan dari `prd.md`. PRD menjelaskan **apa**, dokumen ini menjelaskan **bagaimana** (teknis).
> Untuk aturan desain visual, `prd.md` Bagian 11 adalah acuan utama.

## 1. Tech Stack

| Layer | Pilihan | Catatan |
|---|---|---|
| Bahasa | PHP 8.3 | |
| Framework | Laravel 12 | Monolith MVC |
| Auth | Laravel Breeze (Blade) | Tambah kolom `phone` dan `role` |
| Frontend | Blade + Tailwind CSS + Alpine.js | Bawaan Breeze, tanpa SPA |
| Build | Vite | |
| Database | MySQL 8 (alternatif: SQLite untuk dev cepat) | |
| File storage | Laravel Storage | disk `public` (poster, foto band), disk `local` privat (bukti bayar, foto KTM) |
| QR code | `simplesoftwareio/simple-qrcode` | Generate SVG/PNG di server |
| Scan kamera | `html5-qrcode` (CDN/npm) | Untuk halaman check-in |
| PDF e-tiket (opsional) | `barryvdh/laravel-dompdf` | FR-12 |
| Ekspor | Native PHP `fputcsv` via streamed response | FR-21 |
| Testing | PHPUnit / Pest (feature test) | |
| Font | Google Fonts: Montserrat | |

**Kenapa monolith Laravel:** satu codebase, auth/validasi/upload/migration sudah bawaan, mudah dijelaskan saat presentasi, dan paling kecil risiko error integrasi.

## 2. Gambaran Arsitektur

```
 Browser (HP/Desktop)
        |
        | HTTPS
        v
+------------------------------------------------+
|                 Laravel 12 App                 |
|                                                |
|  routes/web.php                                |
|     |                                          |
|     v                                          |
|  Middleware (auth, role:admin)                 |
|     |                                          |
|     v                                          |
|  Controllers  ->  Form Requests (validasi)     |
|     |                                          |
|     v                                          |
|  Services (OrderService, PaymentService,       |
|            TicketService, CheckinService)      |
|     |                                          |
|     v                                          |
|  Eloquent Models  <->  MySQL                   |
|     |                                          |
|     +-> Storage (public / local-private)       |
|                                                |
|  Blade Views + Tailwind (tema Harfest)         |
|  Scheduler: orders:expire (tiap 5 menit)       |
+------------------------------------------------+
```

Alur request: Route -> Middleware -> Controller -> FormRequest -> Service -> Model -> View/Redirect.

**Prinsip:** controller tipis, logika bisnis di Service, validasi di FormRequest, otorisasi di Policy/Middleware.

## 3. Struktur Folder

```
harfest/
├── app/
│   ├── Console/Commands/ExpireOrders.php
│   ├── Enums/
│   │   ├── EventStatus.php
│   │   ├── OrderStatus.php
│   │   ├── PaymentStatus.php
│   │   └── UserRole.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LandingController.php
│   │   │   ├── TicketController.php            # daftar jenis tiket (event aktif)
│   │   │   ├── EventController.php             # arsip /events dan /events/{slug}
│   │   │   ├── OrderController.php             # create, store, index, show, cancel
│   │   │   ├── PaymentProofController.php      # upload / reupload bukti
│   │   │   ├── ETicketController.php           # tampil / unduh e-tiket
│   │   │   ├── FileController.php              # serve file privat (bukti, KTM) dengan otorisasi
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── EventController.php
│   │   │       ├── BandController.php
│   │   │       ├── TicketTypeController.php
│   │   │       ├── PaymentVerificationController.php
│   │   │       ├── AdminOrderController.php    # list + export CSV
│   │   │       └── CheckinController.php
│   │   ├── Middleware/ (EnsureUserIsAdmin.php, SetAdminEvent.php)
│   │   └── Requests/
│   │       ├── StoreOrderRequest.php
│   │       ├── UploadProofRequest.php
│   │       ├── RejectPaymentRequest.php
│   │       └── (request CRUD admin lainnya)
│   ├── Models/ User, Event, Band, TicketType, Order, Payment, Ticket
│   ├── Policies/OrderPolicy.php
│   └── Services/
│       ├── OrderService.php
│       ├── PaymentService.php
│       ├── TicketService.php
│       ├── CheckinService.php
│       └── EventService.php                # event aktif, duplikat, aturan penjualan
├── config/harfest.php                           # rekening, QRIS, batas bayar, dll
├── database/
│   ├── migrations/
│   └── seeders/ DatabaseSeeder, AdminSeeder, EventSeeder, TicketTypeSeeder, BandSeeder
├── resources/
│   ├── css/app.css                              # token warna, pola zigzag, komponen
│   ├── js/app.js
│   └── views/
│       ├── layouts/ (app.blade.php, admin.blade.php, guest.blade.php)
│       ├── components/ (logo, button, card, status-badge, price-pill, band-frame, mascot, burst, yellow-plate, pattern-divider, eticket, section-title)
│       ├── landing/
│       ├── tickets/
│       ├── orders/
│       ├── etickets/
│       └── admin/ (dashboard, events, bands, ticket-types, payments, orders, checkin)
├── public/images/ (brand/, mascot/, patterns/)
├── docs/reference/harfest-ig.jpeg              # referensi desain
├── routes/web.php
├── tests/Feature/
├── prd.md
├── architecture.md
└── antigravity_prompt.md
```

## 4. Skema Database

Semua tabel memakai `id` bigint auto-increment dan `timestamps` kecuali dinyatakan lain.

### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | |
| email | varchar(150) unique | |
| phone | varchar(20) | |
| password | varchar(255) | hash |
| role | enum('user','admin') | default `user` |

### `events`
| Kolom | Tipe | Keterangan |
|---|---|---|
| name | varchar(150) | Harfest |
| description | text | |
| venue | varchar(150) | |
| event_date | datetime | |
| edition | unsigned smallint | nomor vol (1, 2, ...), unique |
| slug | varchar(50) unique | `vol-1`, `vol-2` |
| tagline | varchar(150) null | teks sub-judul per edisi |
| poster | varchar(255) null | |
| logo | varchar(255) null | logo khusus edisi, kosong = logo default |
| banner | varchar(255) null | |
| status | enum | `draft`, `upcoming`, `on_sale`, `finished` |
| sales_open_at | datetime null | |
| sales_close_at | datetime null | |

### `bands`
| Kolom | Tipe | Keterangan |
|---|---|---|
| event_id | FK events (cascade) | |
| name | varchar(100) | |
| description | text | |
| photo | varchar(255) null | |
| is_headliner | boolean | default false (bintang tamu utama) |
| logo | varchar(255) null | logo band untuk lineup mode kotak berwarna |
| instagram_url | varchar(255) null | |
| sort_order | int | default 0 |

### `ticket_types`
| Kolom | Tipe | Keterangan |
|---|---|---|
| event_id | FK events (cascade) | |
| name | varchar(100) | |
| description | text | |
| price | unsigned int | rupiah |
| quota | unsigned int | dalam jumlah paket |
| people_per_ticket | tinyint | 1 atau 3 |
| requires_student_id | boolean | true untuk civitas |
| is_active | boolean | |

### `orders`
| Kolom | Tipe | Keterangan |
|---|---|---|
| booking_code | varchar(24) unique | `HF{edisi}-YYYYMMDD-XXXX`, contoh `HF2-20261128-0001` |
| event_id | FK events | didenormalisasi dari ticket_types untuk filter cepat |
| user_id | FK users | |
| ticket_type_id | FK ticket_types | |
| quantity | unsigned smallint | |
| total_price | unsigned int | dihitung server |
| student_id | varchar(30) null | NIM/ID |
| student_card_path | varchar(255) null | disk privat |
| status | enum | `pending_payment`, `waiting_verification`, `paid`, `rejected`, `expired`, `cancelled` |
| expires_at | datetime | created_at + 24 jam |

Index: `(user_id)`, `(status)`, `(ticket_type_id, status)`, `(event_id, status)`.

### `payments`
| Kolom | Tipe | Keterangan |
|---|---|---|
| order_id | FK orders (cascade) | |
| proof_path | varchar(255) | disk privat |
| bank_name | varchar(50) null | |
| sender_name | varchar(100) null | |
| status | enum('pending','approved','rejected') | |
| reject_reason | text null | |
| verified_by | FK users null | admin |
| verified_at | datetime null | |

### `tickets`
| Kolom | Tipe | Keterangan |
|---|---|---|
| order_id | FK orders (cascade) | |
| ticket_code | varchar(40) unique | isi QR, acak tidak tertebak (mis. `HF-` + 16 char Str::random uppercase) |
| holder_name | varchar(100) null | |
| is_used | boolean | default false |
| used_at | datetime null | |
| checked_by | FK users null | |

### 4.1 Migrasi dari skema lama (project sudah berjalan)

Buat migration tambahan, **jangan `migrate:fresh` jika sudah ada data yang ingin dipertahankan**:

1. `add_multi_edition_columns_to_events_table`: tambah `edition`, `slug`, `tagline`, `logo`, `banner`, `status`, `sales_open_at`, `sales_close_at`. Hapus `is_active`.
2. `add_headliner_columns_to_bands_table`: tambah `is_headliner`, `logo`, `instagram_url`.
3. `add_event_id_to_orders_table`: tambah `event_id` (nullable dulu), isi dari `ticket_types.event_id` (`UPDATE orders SET event_id = (SELECT event_id FROM ticket_types WHERE ...)`), lalu ubah jadi NOT NULL dan tambah FK dan index.
4. Backfill event lama: `edition = 1`, `slug = 'vol-1'`, `status = 'finished'`. Update seeder agar membuat Vol 1 (arsip) dan Vol 2 (aktif).

## 5. Enum

```php
enum OrderStatus: string {
    case PendingPayment = 'pending_payment';
    case WaitingVerification = 'waiting_verification';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
```

Method helper: `label()` (teks Indonesia), `color()` (untuk badge), `countsTowardQuota()` (true untuk pending_payment, waiting_verification, paid).

```php
enum EventStatus: string {
    case Draft = 'draft';
    case Upcoming = 'upcoming';
    case OnSale = 'on_sale';
    case Finished = 'finished';
}
```

Method helper: `label()` (Segera, Dijual, Selesai, Draft) dan `color()` untuk badge.

Method di model `Event`:
- `isSalesOpen()`: `status === on_sale` **dan** sekarang berada di antara `sales_open_at` dan `sales_close_at` (jika diisi).
- `scopeActive()`: urutan prioritas `on_sale` > `upcoming` > `finished` terbaru.
- `logoUrl()`: `logo` event atau logo default.

## 6. Routes

```php
// Publik
GET  /                                  LandingController@index        name: home
GET  /tickets                           TicketController@index         name: tickets.index   (event aktif)
GET  /events                            EventController@index          name: events.index
GET  /events/{event:slug}               EventController@show           name: events.show

// User (auth)
GET  /orders                            OrderController@index          name: orders.index
GET  /orders/create/{ticketType}        OrderController@create         name: orders.create
POST /orders                            OrderController@store          name: orders.store
GET  /orders/{order:booking_code}       OrderController@show           name: orders.show
POST /orders/{order:booking_code}/cancel        OrderController@cancel
POST /orders/{order:booking_code}/payment       PaymentProofController@store
GET  /orders/{order:booking_code}/tickets       ETicketController@show
GET  /orders/{order:booking_code}/tickets/download   ETicketController@download
GET  /files/payment/{payment}           FileController@payment         (pemilik atau admin)
GET  /files/student-card/{order}        FileController@studentCard     (pemilik atau admin)

// Admin (auth + role:admin), prefix /admin, name admin.*
GET  /admin                             DashboardController@index
resource /admin/event                   EventController
POST /admin/event/{event}/switch        EventController@switch         (simpan event kerja di session)
POST /admin/event/{event}/duplicate     EventController@duplicate
POST /admin/event/{event}/status        EventController@updateStatus
resource /admin/bands                   BandController
resource /admin/ticket-types            TicketTypeController
GET  /admin/payments                    PaymentVerificationController@index
GET  /admin/payments/{payment}          PaymentVerificationController@show
POST /admin/payments/{payment}/approve  PaymentVerificationController@approve
POST /admin/payments/{payment}/reject   PaymentVerificationController@reject
GET  /admin/orders                      AdminOrderController@index
GET  /admin/orders/export               AdminOrderController@export
GET  /admin/checkin                     CheckinController@index
POST /admin/checkin                     CheckinController@verify
```

Semua route admin yang berkaitan dengan data per event (dashboard, payments, orders, checkin, export) memakai middleware `SetAdminEvent` yang membaca `session('admin_event_id')`, default ke event aktif, dan membagikannya ke view serta query.

Semua route `{order}` wajib melewati `OrderPolicy@view` (hanya pemilik atau admin).

## 7. Logika Inti (Services)

### 7.1 OrderService::create(User $user, TicketType $type, int $qty, ?array $civitasData)

```php
DB::transaction(function () {
    // 1. Kunci baris jenis tiket agar tidak oversell
    $type = TicketType::whereKey($typeId)->lockForUpdate()->firstOrFail();

    // 2. Validasi aktif dan sisa kuota
    $used = Order::where('ticket_type_id', $type->id)
        ->whereIn('status', OrderStatus::countingStatuses())
        ->sum('quantity');
    if (! $type->event->isSalesOpen() || ! $type->is_active || $qty > ($type->quota - $used)) {
        throw ValidationException::withMessages(['quantity' => 'Kuota tidak mencukupi.']);
    }

    // 3. Buat order, total dihitung server
    return Order::create([
        'booking_code' => $this->generateBookingCode(),
        'user_id' => $user->id,
        'event_id' => $type->event_id,
        'ticket_type_id' => $type->id,
        'quantity' => $qty,
        'total_price' => $type->price * $qty,
        'status' => OrderStatus::PendingPayment,
        'expires_at' => now()->addHours(config('harfest.payment_deadline_hours')),
        // student_id & student_card_path jika requires_student_id
    ]);
});
```

Accessor `TicketType::remainingQuota()` memakai query yang sama untuk ditampilkan di UI.

### 7.2 PaymentService
- `uploadProof(Order $order, UploadedFile $file, ...)`: izinkan hanya jika status `pending_payment` atau `rejected` dan belum expired. Simpan file ke disk privat dengan nama acak, buat record `payments` (`pending`), set order ke `waiting_verification`.
- `approve(Payment $payment, User $admin)`: dalam transaksi, set payment `approved` (isi `verified_by`, `verified_at`), order `paid`, lalu panggil `TicketService::issue($order)`.
- `reject(Payment $payment, User $admin, string $reason)`: payment `rejected` + alasan, order `rejected`.

### 7.3 TicketService::issue(Order $order)
Generate `quantity * people_per_ticket` record `tickets` dengan `ticket_code` unik. Idempotent: jika order sudah punya tiket, jangan generate ulang.

### 7.4 CheckinService::verify(string $code, User $staff, Event $event)
```
Ticket tidak ditemukan           -> result: invalid
Ticket.order.status != paid      -> result: invalid
Ticket.order.event_id != $event->id -> result: wrong_event ("Tiket untuk event lain")
Ticket.is_used = true            -> result: used (sertakan used_at)
else: set is_used, used_at, checked_by (dalam transaksi + lockForUpdate) -> result: success
```
Return struct/array berisi `result`, data tiket, nama pemesan, jenis tiket.

### 7.5 Scheduler
`app:expire-orders` dijadwalkan tiap 5 menit (`routes/console.php`): ubah `pending_payment` dengan `expires_at < now()` menjadi `expired`.

### 7.6 EventService
- `active(): ?Event`: event `on_sale` dengan tanggal terdekat, lalu `upcoming`, lalu `finished` terbaru. Di-cache pendek (misal 60 detik) dan di-clear saat event berubah.
- `duplicate(Event $source): Event`: dalam transaksi, salin event dengan `edition = max+1`, slug baru, status `draft`, lalu salin `ticket_types` (harga, kuota, people_per_ticket, requires_student_id). **Tidak** menyalin pesanan, tiket, atau band.
- `updateStatus(Event $event, EventStatus $to)`: validasi transisi (draft -> upcoming -> on_sale -> finished), tolak lompatan mundur kecuali admin menyetujui.

### 7.7 Command penutup penjualan
`app:close-finished-sales` (jadwal harian) mengubah event `on_sale` yang `sales_close_at < now()` atau `event_date < now()` menjadi `finished`.

### 7.8 Isolasi data per event
Semua query laporan (dashboard, daftar pesanan, verifikasi, ekspor CSV, check-in) wajib memakai `where('event_id', $event->id)`. Gunakan global scope opsional `Order::forEvent($event)` agar tidak ada query yang lupa filter.

## 8. Upload dan File Handling

| Jenis file | Disk | Path | Akses |
|---|---|---|---|
| Poster event, foto band | `public` | `events/`, `bands/` | Publik via `storage:link` |
| Bukti bayar | `local` (privat) | `payments/` | Hanya via `FileController` dengan cek otorisasi |
| Foto KTM civitas | `local` (privat) | `student-cards/` | Sama |

Validasi upload: `mimes:jpg,jpeg,png,pdf|max:2048` (bukti), `mimes:jpg,jpeg,png|max:2048` (KTM/foto). Nama file selalu di-randomize (`hashName()`).

## 9. Keamanan

- CSRF token di semua form (`@csrf`).
- Middleware `EnsureUserIsAdmin` (cek `role === 'admin'`) untuk grup `/admin`.
- `OrderPolicy` untuk akses pesanan, bukti, dan e-tiket.
- Mass assignment dilindungi (`$fillable`), jangan pernah menerima `role`, `total_price`, `status` dari input user.
- Rate limiting pada login dan endpoint check-in (`throttle`).
- `ticket_code` acak dan tidak berurutan agar tidak bisa ditebak.
- Escape output Blade default (`{{ }}`), hindari `{!! !!}` untuk input user.
- `.env` tidak di-commit. Sediakan `.env.example`.

## 10. Konfigurasi (`config/harfest.php`)

```php
return [
    'payment_deadline_hours' => 24,
    'max_qty_per_order' => 10,
    'bank' => [
        'name' => env('HARFEST_BANK_NAME', 'BCA'),
        'account_number' => env('HARFEST_BANK_ACCOUNT', '1234567890'),
        'account_holder' => env('HARFEST_BANK_HOLDER', 'Panitia Harfest'),
    ],
    'qris_image' => 'images/qris-harfest.png',   // placeholder
    'contact_whatsapp' => env('HARFEST_CONTACT_WA', '6281234567890'),
];
```

## 11. Frontend Architecture (Tema Harfest)

Acuan visual: banner resmi `public/images/brand/harfest-banner.png` dan feed IG (lihat `prd.md` Bagian 11). **Logo dan maskot dipakai sebagai file gambar resmi, bukan dibuat ulang dengan font atau SVG karangan sendiri.**

### 11.1 Aset statis

```
public/images/
├── brand/
│   ├── harfest-banner.png        # banner resmi (hero)
│   ├── harfest-logo.png|svg      # logo plat kuning + lettering (navbar, footer)
│   ├── burst-yellow.svg          # aksen kilat kuning
│   └── burst-blue.svg            # aksen kilat biru
├── mascot/
│   ├── globe.png                 # bola biru berheadphone
│   └── player.png                # music player pink
└── patterns/
    └── zigzag.svg                # zigzag siku tebal, satu warna, tile
```

Jika versi transparan logo/maskot belum ada, pakai banner apa adanya sebagai hero dan jangan membuat ulang aset.

### 11.2 Tailwind config (`tailwind.config.js`)

```js
theme: {
  extend: {
    colors: {
      hf: {
        magenta:        '#9A0474',  // background utama
        'magenta-light':'#9E0A7B',  // pola zigzag
        pink:           '#DE037A',
        purple:         '#4D0570',
        'purple-deep':  '#2D0A4E',
        yellow:         '#FFD000',
        'yellow-dark':  '#E6B800',
        blue:           '#3DB8F0',
      },
    },
    fontFamily: {
      sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      display: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
    },
    boxShadow: {
      hard:    '6px 6px 0 #2D0A4E',
      'hard-sm': '3px 3px 0 #2D0A4E',
      'hard-yellow': '4px 4px 0 #FFD000',
    },
  },
}
```

Muat Montserrat (500, 600, 700, 800, 900) dari Google Fonts di layout.

### 11.3 Pola zigzag

```css
.bg-hf-pattern {
  background-color: theme('colors.hf.magenta');
  background-image: url('/images/patterns/zigzag.svg'); /* garis #9E0A7B */
  background-size: 160px 160px;
}
```

Pola harus **halus (tone-on-tone)**, bukan kontras tinggi. Jangan memakai pola kotak diagonal.

### 11.4 Komponen Blade (`resources/views/components/`)

| Komponen | Fungsi |
|---|---|
| `<x-logo>` | Menampilkan gambar logo resmi (ukuran sm/md/lg) |
| `<x-section-title>` | Judul huruf kapital Montserrat ExtraBold ungu di atas plat kuning tidak beraturan (SVG/clip-path), sedikit miring |
| `<x-yellow-plate>` | Plat kuning bentuk tidak beraturan dengan outline ungu, dipakai ulang untuk tagline dan judul |
| `<x-button>` | Varian `primary` (kuning, teks ungu), `secondary` (pink), `ghost` (outline kuning); hard shadow dan efek tekan |
| `<x-card>` | Rounded-3xl, border tebal ungu, hard shadow |
| `<x-price-pill>` | Bar kuning/pink berisi label dan harga (mis. "Tiket Umum 1 orang 40K") |
| `<x-band-frame>` | Foto grayscale dalam bingkai tebal pink/ungu dan nama band |
| `<x-status-badge>` | Badge status sesuai `OrderStatus::color()` |
| `<x-mascot>` | Prop `type` (`globe` atau `player`), memuat gambar dari `images/mascot/`, animasi float halus, hormati `prefers-reduced-motion` |
| `<x-burst>` | Aksen kilat kuning/biru di sekitar logo atau maskot |
| `<x-pattern-divider>` | Pita zigzag pemisah section (pink di atas ungu) |
| `<x-eticket>` | Kartu e-tiket gaya tiket konser dengan logo resmi dan QR |
| `<x-headliner>` | Section bintang tamu utama: foto cutout beroutline kuning/pink, maskot kiri-kanan, atau kartu "Akan diumumkan" |
| `<x-band-logo-box>` | Kotak kuning/pink selang-seling berisi logo band |
| `<x-event-card>` | Kartu event untuk `/events` dengan badge status |
| `<x-event-switcher>` | Dropdown event kerja di sidebar admin |

### 11.5 Layout
- `layouts/app.blade.php`: navbar (`<x-logo>`, menu, tombol Masuk dan Beli Tiket), slot konten, footer (logo, kredit "In collaboration with Jendela Creative Corps").
- `layouts/admin.blade.php`: sidebar (Dashboard, Event, Band, Tiket, Verifikasi, Pesanan, Check-in), palet sama tapi lebih padat dan tabel jelas, background lebih netral.
- `layouts/guest.blade.php`: login/register, card di tengah dengan background pola magenta dan maskot.

### 11.6 Susunan halaman landing
1. Hero: banner resmi full width (`object-cover`), lalu background magenta menyatu di bawahnya berisi tanggal, lokasi, tombol, dan price-pill.
2. Tentang event (plat kuning + teks).
3. Lineup: grid `<x-band-frame>` dari database.
4. Harga tiket: 3 `<x-card>` dengan `<x-price-pill>`.
5. CTA dan footer.

Section diselang-seling magenta (dominan) dan ungu `hf-purple`, dipisahkan `<x-pattern-divider>`.

## 12. Testing

Feature test minimal (PHPUnit/Pest):

1. User bisa register dan login.
2. User tidak login diarahkan ke login saat mengakses `/orders`.
3. Buat pesanan sukses: `total_price` benar, status `pending_payment`, kode booking unik.
4. Buat pesanan gagal jika melebihi kuota.
5. Dua pesanan beruntun pada kuota tersisa 1: hanya satu berhasil.
6. Upload bukti: format salah ditolak, file > 2 MB ditolak, sukses mengubah status ke `waiting_verification`.
7. User A tidak bisa melihat pesanan atau bukti bayar user B.
8. User biasa mendapat 403 di `/admin`.
9. Admin approve: status `paid` dan jumlah tiket = `quantity * people_per_ticket`.
10. Admin reject tanpa alasan: validasi gagal.
11. Check-in tiket valid: sukses. Check-in kedua kali: ditolak (used). Kode palsu: invalid.
12. Command `app:expire-orders` mengubah pesanan lewat batas menjadi `expired` dan mengembalikan kuota.
13. Pesanan untuk event `finished` atau `draft` ditolak.
14. Kuota Vol 1 dan Vol 2 terpisah: pembelian di Vol 2 tidak mengurangi kuota Vol 1.
15. Check-in tiket Vol 1 pada event kerja Vol 2 menghasilkan `wrong_event`.
16. `EventService::duplicate()` menyalin jenis tiket tapi tidak menyalin pesanan dan band.
17. Admin yang mengganti event switcher melihat data dashboard dan ekspor CSV sesuai event terpilih.
18. `/events/vol-1` tampil dengan badge Selesai dan tanpa tombol beli, sedangkan `/events/vol-2` tampil dengan tombol beli.

## 13. Setup dan Menjalankan

```bash
composer create-project laravel/laravel harfest
cd harfest
composer require laravel/breeze --dev
php artisan breeze:install blade
composer require simplesoftwareio/simple-qrcode
# opsional: composer require barryvdh/laravel-dompdf
cp .env.example .env && php artisan key:generate
# atur DB di .env
php artisan migrate:fresh --seed
php artisan storage:link
npm install && npm run dev
php artisan serve
```

Untuk scheduler lokal: `php artisan schedule:work`.

## 14. Deployment (opsional)

Untuk demo cukup `php artisan serve` di laptop. Jika butuh online gratis: hosting PHP gratis atau VPS kecil, set `APP_ENV=production`, `APP_DEBUG=false`, jalankan `php artisan migrate --force`, `config:cache`, `route:cache`, `view:cache`, dan `npm run build`.

## 15. Konvensi Kode dan Git

- PSR-12, nama class PascalCase, method camelCase, tabel snake_case jamak.
- Branch: `main` (stabil), `dev` (integrasi), `feature/<nama-fitur>`.
- Commit message: `feat: ...`, `fix: ...`, `docs: ...`, `test: ...`, `style: ...`.
- Satu card Trello = satu atau beberapa commit yang menyebut nama card (mis. `feat(order): buat pesanan tiket [Trello #13]`). Ini membantu bukti kontribusi tim.
