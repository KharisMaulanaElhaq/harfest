# Harfest Ticketing System (Harkat Negeri Fest 2026)

> **Web App Pemesanan Tiket Konser Kampus Universitas Harkat Negeri**  
> Menggantikan proses manual berbasis WhatsApp / Google Forms dengan alur online terintegrasi: pemilihan tiket, pembayaran transfer & upload bukti, verifikasi admin, penerbitan e-tiket QR code, scanner check-in di hari H, serta panel admin lengkap.

---

## 🎨 Identitas & Desain Visual
Tampilan web ini terinspirasi langsung dari feed Instagram `@harkatnegerifest`:
- **Palet Warna**: Ungu Gelap (`#4B1180`, `#2D0A4E`), Hot-Pink (`#E4157F`, `#F24BA0`), Kuning Terang (`#FFD60A`), dan Biru Langit (`#4DC3F7`).
- **Tipografi**: Display font **Lilita One** (tebal, playful, outline/shadow ungu) dan teks isi **Poppins**.
- **Aksen Visual**: Pola latar belakang SVG zigzag/squiggle pink (`.bg-hf-pattern`), maskot retro handheld device ungu dan blob biru menggemaskan, bingkai foto band monokrom, serta kartu e-tiket dengan scalloped notches dan perforasi sobekan.
- **Responsif**: Dioptimalkan untuk semua ukuran layar (viewport mobile 375px hingga desktop layar lebar).

---

## ✨ Fitur-Fitur Utama

### 1. Publik & Pengguna (User)
- **Landing Page Interaktif**:
  - Hero banner bertema playful dengan maskot dan countdown hitung mundur konser secara real-time.
  - Lineup band kampus (frame foto retro grayscale, bio, urutan tampil).
  - Kartu kategori tiket dinamis dengan kuota real-time dan badge "HABIS" otomatis jika kuota 0.
  - Section tentang Harfest, jadwal & denah panggung, FAQ accordion interaktif, serta kontak WhatsApp.
- **Pemesanan Tiket Aman**:
  - Validasi kuota atomik menggunakan database row locking (`lockForUpdate()`) untuk mencegah *overselling*.
  - Dukungan tiket paket rombongan (misal: "Umum 3 Orang" bernilai 3 kuota masuk).
  - Form khusus kategori Civitas: wajib input NIM dan upload kartu identitas mahasiswa (KTM) yang disimpan di direktori privat.
  - Penghitungan total harga di server untuk mencegah manipulasi client-side.
  - Nomor booking unik berformat `HF-YYYYMMDD-XXXX`.
- **Pembayaran Manual & Unggah Bukti**:
  - Info nomor rekening resmi, penerima, dan gambar QRIS.
  - Countdown batas waktu pembayaran (24 jam).
  - Upload bukti transfer (JPG, PNG, PDF maks 2 MB) ke storage privat.
  - Alur penolakan & upload ulang jika bukti transfer tidak jelas/valid.
  - Fitur pembatalan pesanan mandiri selama status masih `pending_payment`.
- **E-Tiket Konser Interaktif**:
  - Desain kartu tiket konser otentik dengan lekukan samping (*scalloped notches*), garis putus-putus perforasi, dan watermark maskot.
  - Kode tiket unik `HF-` + 16 karakter acak tak tertebak.
  - QR code SVG tajam (menggunakan `simplesoftwareio/simple-qrcode`) siap scan di pintu masuk.
  - Tombol Cetak / Simpan PDF langsung dari browser dengan media print CSS teroptimasi.

### 2. Panel Admin (`/admin`)
- **Dashboard Eksekutif**:
  - Ringkasan statistik Harfest: Total Tiket Terjual (Paid), Total Pendapatan, Pesanan Menunggu Verifikasi, dan Total Pesanan Lunas.
  - Monitor sisa kuota tiap jenis tiket dengan progress bar persentase visual.
  - Tabel 5 pesanan terbaru dengan shortcut aksi cepat.
- **Manajemen Pesanan (`/admin/orders`)**:
  - Tabel komprehensif seluruh pesanan dengan filter status dan pencarian live (kode booking / nama pengguna).
  - **Ekspor CSV Streamed Response**: Dilengkapi dengan UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) agar langsung terbaca rapi dengan kolom terpisah di Microsoft Excel tanpa wizard impor.
- **Verifikasi Pembayaran (`/admin/payments`)**:
  - Daftar antrean pesanan yang menunggu verifikasi.
  - Detail tinjauan bukti transfer dan foto KTM mahasiswa dengan zoom viewer.
  - Tombol Terima (mengubah pesanan menjadi `paid` dan otomatis menerbitkan e-tiket).
  - Tombol Tolak dengan alasan wajib (dicatat dan ditampilkan ke pengguna untuk upload ulang).
- **Gate Check-in Scanner (`/admin/checkin`)**:
  - Mode **Kamera Scanner**: Scan QR code e-tiket langsung dari webcam / kamera HP via `html5-qrcode`.
  - Mode **Input Manual**: Masukkan kode tiket secara manual jika kamera tidak tersedia.
  - Tampilan hasil besar dan kontras:
    - 🟢 **Hijau (SUKSES)**: Check-in berhasil, menampilkan nama pemesan dan jenis tiket.
    - 🔴 **Merah (USED)**: Tiket sudah dipakai sebelumnya beserta timestamp pemakaian.
    - 🔴 **Merah (INVALID)**: Kode tiket tidak terdaftar atau belum lunas.
  - Dilengkapi rate limiter (`throttle:60,1`) untuk mencegah brute-force scanning.
- **CRUD Master Data**:
  - **Event**: Kelola nama konser, tanggal, venue, deskripsi, status aktif, dan poster banner.
  - **Band Lineup**: Tambah band, deskripsi, urutan tampil (`sort_order`), dan upload foto.
  - **Jenis Tiket**: Atur nama, deskripsi, harga, kuota, jumlah orang per tiket (`people_per_ticket`), dan status aktif.
  - *Validasi Kuota Ketat*: Kuota tidak dapat diturunkan di bawah jumlah pesanan yang sudah berkomitmen/terjual.

### 3. Otomatisasi & Pemeliharaan
- **Order Expiration Worker (`app:expire-orders`)**:
  - Otomatis mengubah pesanan `pending_payment` yang melewati batas `expires_at` menjadi `expired`.
  - Mengembalikan kuota tiket secara otomatis ke sistem.
  - Dijadwalkan berjalan tiap 5 menit melalui scheduler Laravel.
- **Custom Error Pages Bertema Harfest**:
  - Halaman 403 (Akses Dibatasi), 404 (Halaman Tidak Ditemukan), dan 500 (Gangguan Server) lengkap dengan maskot ekspresif, pesan santun, dan tombol kembali ke panggung utama.

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| **Bahasa & Runtime** | PHP 8.3+ |
| **Framework** | Laravel 12 (Monolith MVC Architecture) |
| **Autentikasi** | Laravel Breeze (Blade stack) |
| **Frontend & Styling** | Blade Components + Tailwind CSS + Alpine.js |
| **Build Tool** | Vite |
| **Database** | MySQL 8.0+ |
| **QR Code Generator** | `simplesoftwareio/simple-qrcode` |
| **Camera QR Scanner** | `html5-qrcode` (npm bundled) |
| **File Storage** | Laravel Storage (Disk `public` untuk poster/foto band; Disk `local` privat untuk bukti & KTM) |
| **Testing** | PHPUnit (64 tests, 309 assertions — 100% PASS) |

---

## 👥 Akun Demo

| Role | Email | Password | Akses |
|---|---|---|---|
| **Administrator** | `admin@harfest.test` | `password` | Akses penuh dashboard, CRUD, verifikasi, dan gate check-in |
| **User (Demo)** | `user@harfest.test` | `password` | Pemesanan tiket, upload bukti, riwayat, dan e-tiket |

---

## 🚀 Panduan Instalasi dan Menjalankan Lokal

### 1. Prasyarat
- PHP >= 8.2 (disarankan PHP 8.3) dengan ekstensi `pdo_mysql`, `gd`, `fileinfo`, `mbstring`.
- Composer.
- Node.js (v18+) & NPM.
- Server database MySQL (misalnya melalui Laragon, XAMPP, atau Docker).

### 2. Kloning & Dependensi
```bash
# Masuk ke direktori project
cd harfest

# Install dependensi PHP
composer install

# Install dependensi JavaScript
npm install
```

### 3. Konfigurasi Environment
Salin berkas `.env.example` ke `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database MySQL di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harfest
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeder Database
Jalankan migrasi bersih beserta seeder data default Harfest:
```bash
php artisan migrate:fresh --seed
```
*Seeder akan membuat akun admin, user demo, event Harfest 2026, 3 kategori tiket (Civitas, Umum 1 Orang, Umum 3 Orang), dan lineup band (Algorhythm, Kalarupa, Substring, Matraman).*

### 5. Storage Symlink
Buat tautan simbolik untuk aset publik:
```bash
php artisan storage:link
```

### 6. Build Frontend & Menjalankan Server
Jalankan build Vite dan server lokal Laravel:
```bash
# Build aset Tailwind & JS
npm run build

# Jalankan server lokal Laravel
php artisan serve
```
Buka browser di: **`http://127.0.0.1:8000`**

---

## ⏱️ Menjalankan Scheduler (Expire Orders)

Untuk menguji fitur otomatisasi pembatalan pesanan yang kedaluwarsa:

1. **Jalankan command manual (sekali jalan)**:
   ```bash
   php artisan app:expire-orders
   ```
2. **Jalankan worker scheduler lokal**:
   ```bash
   php artisan schedule:work
   ```
   Scheduler akan mengeksekusi `app:expire-orders` tiap 5 menit sesuai konfigurasi di `routes/console.php`.

---

## 🧪 Pengujian Otomatis (Feature Tests)

Proyek ini dilengkapi test suite komprehensif yang mencakup 12 skenario arsitektur utama:
1. Registrasi & login pengguna baru.
2. Tamu (guest) diarahkan ke login saat mengakses halaman pesanan.
3. Pembuatan pesanan reguler (total harga server-side, kode booking unik, status pending).
4. Penolakan pesanan jika kuota tidak mencukupi.
5. Konkurensi 2 pesanan beruntun pada sisa kuota 1: hanya 1 yang berhasil.
6. Validasi bukti pembayaran (ekstensi salah & ukuran > 2 MB ditolak, file valid diterima).
7. Otorisasi kepemilikan pesanan (User A tidak bisa melihat pesanan/bukti bayar User B).
8. Proteksi route admin (User biasa menerima respon 403 Forbidden).
9. Verifikasi persetujuan admin: status menjadi `paid` dan jumlah tiket = `quantity * people_per_ticket`.
10. Validasi penolakan pembayaran: alasan penolakan wajib diisi.
11. Verifikasi gate check-in: tiket valid sukses, tiket dipakai kedua kali ditolak (`used`), kode palsu ditolak (`invalid`).
12. Artisan command `app:expire-orders` mengubah pesanan kedaluwarsa menjadi `expired` dan mengembalikan kuota.

Jalankan seluruh test suite dengan:
```bash
php artisan test
```
**Hasil Test**: **64 passed, 0 failed, 309 assertions**.

---

## 📂 Struktur Direktori Utama

```
harfest/
├── app/
│   ├── Console/Commands/
│   │   └── ExpireOrders.php                # Command pembatalan pesanan kedaluwarsa
│   ├── Enums/                              # OrderStatus, PaymentStatus, UserRole
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LandingController.php       # Beranda publik dinamis
│   │   │   ├── TicketController.php        # Katalog tiket & e-tiket
│   │   │   ├── OrderController.php         # Pembuatan & riwayat pesanan
│   │   │   ├── PaymentProofController.php  # Unggah bukti bayar
│   │   │   ├── FileController.php          # Streaming file privat aman
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php # Metrik & statistik Harfest
│   │   │       ├── AdminOrderController.php# Tabel & streamed CSV export
│   │   │       ├── PaymentVerificationController.php
│   │   │       ├── CheckinController.php   # Gate scanner & verifikasi
│   │   │       ├── EventController.php     # CRUD Event
│   │   │       ├── BandController.php      # CRUD Lineup Band
│   │   │       └── TicketTypeController.php# CRUD Jenis Tiket
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php       # Proteksi role admin
│   │   └── Requests/                       # Form validation classes
│   ├── Models/                             # Event, Band, TicketType, Order, Payment, Ticket, User
│   ├── Policies/
│   │   └── OrderPolicy.php                 # Otorisasi pesanan & berkas
│   └── Services/
│       ├── OrderService.php                # Transaksi atomik & locking kuota
│       ├── PaymentService.php              # Upload, approve, reject pembayaran
│       ├── TicketService.php               # Penerbitan e-tiket idempotent
│       └── CheckinService.php              # Verifikasi check-in pintu masuk
├── config/
│   └── harfest.php                         # Pengaturan rekening, QRIS, deadline bayar
├── database/
│   ├── migrations/                         # Skema database relasional
│   └── seeders/                            # Data awal akun, event, tiket, band
├── resources/
│   ├── css/app.css                         # Token warna, font, pattern zigzag, print CSS
│   ├── js/app.js                           # Alpine.js & Html5Qrcode bundle
│   └── views/
│       ├── components/                     # Komponen UI bertema Harfest
│       ├── errors/                         # 403, 404, 500 bertema Harfest
│       ├── landing/                        # Landing page panggung utama
│       ├── orders/                         # Form pesan, riwayat, detail, tiket
│       └── admin/                          # Panel admin lengkap
└── routes/
    ├── web.php                             # Rute web publik, user, dan admin
    └── console.php                         # Penjadwalan command tiap 5 menit
```

---

## 💡 Catatan Keputusan Teknis (Architectural Decisions)

1. **Arsitektur Monolith MVC**:
   - Memilih arsitektur Laravel Monolith dengan Blade + Tailwind + Alpine.js daripada SPA terpisah (React/Vue). Keputusan ini memangkas overhead API auth, mempercepat render SEO panggung utama, dan menjamin integritas validasi form/CSRF secara terpusat.
2. **Pencegahan Overselling (Atomic Quota Locking)**:
   - Menggunakan `DB::transaction()` bersama `lockForUpdate()` pada baris `ticket_types` saat pesanan dibuat.
   - Sisa kuota dihitung dinamis dari `quota - SUM(quantity)` pada pesanan berstatus `pending_payment`, `waiting_verification`, dan `paid`. Pesanan berstatus `cancelled` dan `expired` otomatis tidak mengurangi kuota sehingga kuota langsung kembali tanpa perlu manipulasi manual.
3. **Multi-Pax Ticket Multiplication**:
   - Jenis tiket paket (misal: "Umum 3 Orang", `people_per_ticket = 3`) saat disetujui admin akan secara idempotent menerbitkan `quantity * people_per_ticket` kartu tiket individual. Setiap orang mendapatkan kode tiket dan QR code unik tersendiri untuk masuk gerbang.
4. **Penyimpanan Berkas Privat & Otorisasi**:
   - Foto KTM mahasiswa dan bukti transfer bank disimpan di disk privat `local` (`storage/app/private/`), BUKAN di disk publik.
   - Akses file dilindungi melalui `FileController` yang memeriksa kepemilikan via `OrderPolicy` atau status admin. Berkas disajikan melalui HTTP response stream, mencegah pencurian berkas identitas atau manipulasi URL publik.
5. **Ekspor CSV Streamed Response dengan UTF-8 BOM**:
   - Pada `/admin/orders/export`, respon CSV dialirkan menggunakan `response()->stream()` agar hemat memori pada volume data besar.
   - File diawali dengan byte khusus UTF-8 BOM (`\xEF\xBB\xBF`) sehingga Microsoft Excel pada sistem Windows langsung mengenali format teks, koma pemisah, dan karakter khusus secara rapi tanpa memunculkan wizard impor teks.
6. **Keamanan & Rate Limiting Gate Scanner**:
   - Endpoint check-in dibatasi dengan middleware `throttle:60,1` (maksimal 60 scan per menit) guna memitigasi potensi serangan brute force kode tiket di gerbang masuk.
   - Operasi check-in dibungkus dalam database transaction dengan row locking untuk mencegah *race condition* (misalnya dua gate scanner memindai kode tiket yang sama pada detik yang bersamaan).
