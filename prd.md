# PRD: Harfest Ticketing System

> Dokumen ini adalah sumber kebenaran (source of truth) untuk fitur dan perilaku sistem.
> Baca juga `architecture.md` (arsitektur teknis) dan `antigravity_prompt.md` (urutan eksekusi).

## 1. Ringkasan

**Harfest (Harkat Negeri Fest)** adalah event musik kampus Universitas Harkat Negeri. Sistem ini adalah **web app pemesanan tiket konser** yang menggantikan proses manual (chat WhatsApp ke panitia) dengan alur online: pilih tiket, bayar via transfer, upload bukti, diverifikasi admin, lalu terbit e-tiket ber-QR.

Sistem dirancang **multi-edisi**: Harfest adalah seri event (Vol 1, Vol 2, dst.), dan satu sistem melayani semua edisi tanpa data tercampur. Vol 1 (headliner Enau) sudah selesai dan menjadi arsip, Vol 2 adalah edisi aktif.

Konteks: tugas mata kuliah Rekayasa Perangkat Lunak (RPL). Penilaian utama ada di **kerja tim dan progres di Trello**, bukan kecanggihan sistem. Maka scope dijaga **realistis dan lengkap**, bukan kompleks.

## 2. Tujuan dan Non-Tujuan

**Tujuan**
- Pembeli bisa memesan tiket dan mendapat e-tiket QR tanpa chat manual.
- Admin bisa memverifikasi pembayaran dan mengelola kuota dengan mudah.
- Petugas gerbang bisa memvalidasi tiket di hari H dan mencegah tiket ganda.
- Sistem tetap berjalan untuk edisi berikutnya (Vol 2, 3, ...) dengan bintang tamu, lineup, dan tiket berbeda, sementara edisi lama tersimpan sebagai arsip.

**Non-tujuan (di luar scope)**
- Payment gateway otomatis (Midtrans, dll). Pembayaran = transfer manual + upload bukti.
- Refund otomatis, multi-event kompleks, notifikasi email/WhatsApp otomatis, aplikasi mobile native.

## 3. Aktor

| Aktor | Deskripsi |
|---|---|
| **Pengunjung** | Belum login. Bisa lihat landing page, lineup, harga tiket. |
| **Pembeli (user)** | Sudah login. Memesan tiket, upload bukti, lihat e-tiket. |
| **Admin / Panitia** | Mengelola event, band, tiket, memverifikasi pembayaran, melihat dashboard. |
| **Petugas gerbang** | Memvalidasi tiket di hari H. Dalam implementasi memakai akun role `admin` (fitur check-in ada di panel admin). |

## 4. Jenis Tiket (data awal / seeder)

| Nama | Harga | Orang per tiket | Catatan |
|---|---|---|---|
| Tiket Civitas Harkat Negeri | Rp30.000 | 1 | Khusus mahasiswa, dosen, alumni, karyawan Universitas Harkat Negeri. Wajib NIM/ID dan foto KTM/kartu identitas. |
| Tiket Umum 1 Orang | Rp40.000 | 1 | Untuk masyarakat umum atau non-WNI. |
| Tiket Umum 3 Orang | Rp100.000 | 3 | Paket bundling, 1 pembelian berlaku untuk 3 orang. Kurang lebih hemat dibanding 3x tiket 1 orang. |

Kuota awal (bisa diubah admin): Civitas 300, Umum 1 Orang 150, Umum 3 Orang 50 (kuota dihitung dalam **jumlah paket**).

**Lineup band (seeder):** Algorhythm, Kalarupa, Substring, Matraman. Tambahkan band lain sebagai data placeholder jika perlu. Deskripsi singkat tiap band diisi dummy yang masuk akal.

### Data seeder per edisi

| Edisi | Status | Headliner | Lineup | Catatan |
|---|---|---|---|---|
| **Vol 1** (`vol-1`) | `finished` | **Enau** | Artura, Kalarupa, Matraman, Substring, Algorhythm (+1 band lagi di poster, nama perlu dikonfirmasi) | Tanggal pelaksanaan diisi sesuai kenyataan. Tiket 3 jenis seperti di atas. Data pesanan **dummy**, bukan data asli. |
| **Vol 2** (`vol-2`) | `on_sale` | "Akan diumumkan" (bisa diubah admin) | Placeholder | Dibuat lewat fitur **Duplikat Event** dari Vol 1. Tanggal sementara 28 November 2026, ganti sesuai jadwal asli. |

Kuota, harga, tanggal, dan bintang tamu **tidak boleh hardcode di view**, semuanya dari database per edisi.

## 5. Alur Utama

### 5.1 Alur pembelian
1. Pengunjung membuka landing page, melihat info event, lineup, dan harga.
2. Klik **Beli Tiket**, jika belum login diarahkan ke login/register.
3. Pembeli memilih jenis tiket dan jumlah. Jika Civitas, isi NIM/ID dan upload foto KTM.
4. Sistem membuat pesanan dengan status `pending_payment`, kode booking unik, dan batas bayar 24 jam.
5. Halaman pembayaran menampilkan nominal, kode booking, nomor rekening/QRIS panitia, dan countdown.
6. Pembeli transfer di luar sistem, lalu upload bukti. Status menjadi `waiting_verification`.
7. Admin membuka daftar verifikasi, melihat bukti, lalu **Terima** atau **Tolak (wajib alasan)**.
8. Jika diterima: status `paid` dan e-tiket QR terbit. Jika ditolak: status `rejected`, pembeli bisa upload ulang.
9. Di hari H, petugas memasukkan/scan kode tiket. Valid dan belum dipakai berarti check-in berhasil.

### 5.2 Status pesanan

`pending_payment` -> (upload bukti) -> `waiting_verification` -> (admin terima) -> `paid`

- `waiting_verification` -> (admin tolak) -> `rejected` -> (upload ulang) -> `waiting_verification`
- `pending_payment` -> (lewat `expires_at`) -> `expired`
- `pending_payment` -> (pembeli batalkan) -> `cancelled`

Pesanan `expired` dan `cancelled` **tidak lagi menghitung kuota**.

### 5.3 Siklus hidup event

`draft` (disiapkan admin, tidak tampil publik) -> `upcoming` (tampil, penjualan belum dibuka) -> `on_sale` (penjualan dibuka) -> `finished` (acara selesai, jadi arsip).

- Landing page menampilkan **event aktif**: event `on_sale`, jika tidak ada maka `upcoming` terdekat. Jika tidak ada keduanya, tampilkan event `finished` terbaru dengan banner "Penjualan sudah ditutup".
- Event `finished` hanya bisa dilihat di halaman arsip, tombol beli nonaktif.
- Penjualan otomatis tertutup jika melewati `sales_close_at`, meskipun status masih `on_sale`.

## 6. Kebutuhan Fungsional

Prioritas: **M** = Must, **S** = Should, **C** = Could.

### Pembeli
| ID | Kebutuhan | Acceptance Criteria | P |
|---|---|---|---|
| FR-01 | Registrasi | Field: nama, email (unik), no. HP, password (min 8, konfirmasi). Password di-hash. | M |
| FR-02 | Login/logout | Salah kredensial menampilkan pesan error. Sukses redirect ke halaman riwayat. | M |
| FR-03 | Landing page | Menampilkan hero, info tanggal/lokasi, lineup band (dari DB), harga tiket, tombol Beli. | M |
| FR-04 | Lihat jenis tiket | Menampilkan nama, harga, deskripsi, sisa kuota. Kuota 0 tampil "Sold Out" dan tombol nonaktif. | M |
| FR-05 | Buat pesanan | Pilih jenis dan jumlah (1-10 paket). Tolak jika melebihi sisa kuota. Terbit `booking_code` unik. | M |
| FR-06 | Tiket Civitas | Wajib isi NIM/ID dan upload foto KTM (JPG/PNG maks 2 MB). | S |
| FR-07 | Halaman pembayaran | Tampil kode booking, total, rekening/QRIS, countdown batas bayar. | M |
| FR-08 | Upload bukti bayar | JPG/PNG/PDF maks 2 MB. Status berubah ke `waiting_verification`. | M |
| FR-09 | Riwayat pesanan | Daftar pesanan milik user dengan badge status. User hanya melihat pesanannya sendiri. | M |
| FR-10 | Upload ulang | Pesanan `rejected` menampilkan alasan dan form upload ulang. | S |
| FR-11 | E-tiket QR | Hanya tampil jika `paid`. Berisi QR, kode tiket, nama pemesan, jenis tiket, info event. | M |
| FR-12 | Unduh e-tiket | Tombol unduh sebagai PNG/PDF. | C |
| FR-13 | Batalkan pesanan | Hanya status `pending_payment`. Kuota otomatis kembali. | C |

### Admin
| ID | Kebutuhan | Acceptance Criteria | P |
|---|---|---|---|
| FR-14 | Akses admin | Route `/admin/*` hanya untuk role `admin`, selain itu 403. | M |
| FR-15 | CRUD event dan band | Tambah, ubah, hapus, upload foto band dan poster. | M |
| FR-16 | CRUD jenis tiket | Harga, kuota, orang per tiket, aktif/nonaktif. Kuota tidak boleh di bawah jumlah terjual. | M |
| FR-17 | Daftar verifikasi | List pesanan `waiting_verification`, bisa preview bukti bayar (gambar/PDF). | M |
| FR-18 | Terima/Tolak | Terima: status `paid`, tiket digenerate. Tolak: alasan wajib. Catat `verified_by` dan `verified_at`. | M |
| FR-19 | Dashboard | Total tiket terjual, total pendapatan (hanya `paid`), sisa kuota per jenis, jumlah pesanan pending. | S |
| FR-20 | Check-in tiket | Input kode atau scan kamera. Hasil: valid (hijau), sudah dipakai (merah + waktu pakai), tidak ditemukan (merah). | M |
| FR-21 | Ekspor pesanan | Unduh CSV semua pesanan. | C |

### Multi-Edisi
| ID | Kebutuhan | Acceptance Criteria | P |
|---|---|---|---|
| FR-22 | Event aktif di landing | Landing dan `/tickets` memakai event aktif. Tanggal, lokasi, headliner, lineup, dan harga dari database. | M |
| FR-23 | Arsip event | `/events` menampilkan daftar event (aktif di atas, selesai di bawah). `/events/{slug}` menampilkan poster/logo, tanggal, lokasi, headliner, lineup, badge "Selesai", tombol beli nonaktif. | M |
| FR-24 | Penjualan per event | Pesanan hanya bisa dibuat untuk event `on_sale` dalam rentang `sales_open_at` s/d `sales_close_at`. Selain itu ditolak dengan pesan jelas. | M |
| FR-25 | Check-in per event | Petugas memilih event saat check-in. Tiket dari event lain ditolak ("Tiket untuk event lain"). | M |
| FR-26 | Event switcher admin | Dropdown di sidebar admin memilih event kerja. Dashboard, verifikasi, pesanan, dan ekspor CSV ter-filter per event. | M |
| FR-27 | Duplikat event | Tombol di admin menyalin event beserta jenis tiketnya (tanpa pesanan, tanpa band) dengan status `draft`, lalu admin mengubah tanggal, harga, kuota, headliner. | S |
| FR-28 | Headliner | Band bisa ditandai `is_headliner`. Landing menampilkan section "Bintang Tamu Utama". Jika belum ada, tampil "Akan diumumkan". | S |
| FR-29 | Logo/banner per event | Event boleh punya logo dan banner sendiri. Jika kosong, pakai logo default. | C |
| FR-30 | Kode booking memuat edisi | Format `HF{edisi}-YYYYMMDD-XXXX`, contoh `HF2-20261128-0001`. | S |

## 7. Aturan Bisnis

- **Kuota**: sisa kuota = `quota` - jumlah `quantity` pesanan berstatus `pending_payment`, `waiting_verification`, `paid`. Dihitung dalam transaksi database dengan lock agar tidak oversell.
- **Batas bayar**: 24 jam sejak pesanan dibuat. Command terjadwal mengubah pesanan lewat batas menjadi `expired`.
- **Jumlah tiket digenerate** = `quantity` x `people_per_ticket`. Contoh: beli 2 paket Umum 3 Orang menghasilkan 6 e-tiket (1 QR per orang).
- **Total harga** = `price` x `quantity`, dihitung di server (jangan percaya input dari client).
- **Kepemilikan**: user hanya boleh mengakses pesanan, bukti bayar, dan tiket miliknya.
- **Bukti bayar** disimpan di disk privat, hanya bisa diakses pemilik pesanan dan admin.
- **Check-in** bersifat sekali pakai, tiket yang sudah `is_used` tidak boleh dipakai ulang.

- **Isolasi edisi**: pesanan, tiket, kuota, dan laporan selalu dihitung per event. Kuota Vol 1 tidak mempengaruhi Vol 2.
- **Arsip bersifat baca-saja untuk publik**: event `finished` tidak bisa dibeli. Data penjualan tetap ada di admin untuk laporan.
- **Event aktif tunggal untuk penjualan**: boleh ada beberapa event `on_sale`, tapi landing memakai yang tanggalnya paling dekat.
- **Check-in** hanya valid jika `orders.event_id` sama dengan event yang dipilih petugas.
- **Data arsip publik** hanya berisi info acara (tanggal, lokasi, lineup, poster). Tidak menampilkan data pembeli.

## 8. Skema Data (ringkas)

Detail kolom lengkap ada di `architecture.md`. Tabel: `users`, `events`, `bands`, `ticket_types`, `orders`, `payments`, `tickets`.

Relasi: users 1-N orders; events 1-N orders (kolom `event_id` didenormalisasi agar filter per event cepat); events 1-N ticket_types; events 1-N bands; ticket_types 1-N orders; orders 1-N payments; orders 1-N tickets.

## 9. Daftar Halaman

**Publik / Pembeli**
| Route | Halaman |
|---|---|
| `/` | Landing (hero, tentang event, lineup, harga tiket, CTA) |
| `/tickets` | Pilih jenis tiket |
| `/login`, `/register` | Auth |
| `/orders/create/{ticketType}` | Form pesanan |
| `/orders` | Riwayat pesanan |
| `/orders/{booking_code}` | Detail pesanan + pembayaran + upload bukti |
| `/orders/{booking_code}/tickets` | E-tiket QR |

**Admin**
| Route | Halaman |
|---|---|
| `/admin` | Dashboard |
| `/admin/event`, `/admin/bands`, `/admin/ticket-types` | CRUD |
| `/admin/payments` | Daftar verifikasi dan detail |
| `/admin/orders` | Semua pesanan + ekspor CSV |
| `/admin/checkin` | Validasi tiket hari H |

**Multi-edisi (publik)**
| Route | Halaman |
|---|---|
| `/events` | Daftar semua event (aktif dan arsip) |
| `/events/{slug}` | Detail event: poster/logo, tanggal, lokasi, headliner, lineup, harga (jika masih dijual) |

**Multi-edisi (admin)**
| Route | Halaman |
|---|---|
| `/admin/event` | Daftar event + tombol Duplikat dan Ubah Status |
| Sidebar admin | Dropdown event switcher |

## 10. Kebutuhan Non-Fungsional

- **Keamanan**: CSRF aktif, validasi server-side semua input, password hash bcrypt, policy/middleware untuk otorisasi, upload dibatasi tipe dan ukuran, nama file di-randomize.
- **Performa**: halaman utama dimuat < 3 detik di jaringan biasa, gambar dioptimalkan.
- **Responsif**: **mobile-first**. Mayoritas pembeli membuka dari HP (link dari Instagram).
- **Kompatibilitas**: Chrome, Safari, Firefox versi terbaru.
- **Bahasa UI**: Bahasa Indonesia, nada santai tapi jelas.
- **Aksesibilitas**: kontras teks memadai (teks putih/kuning di atas magenta/ungu, teks ungu di atas kuning), ukuran tap target min 44px.

## 11. Desain Visual (WAJIB mengikuti aset resmi dan feed Instagram @harkatnegerifest)

Acuan visual ada dua dan **keduanya wajib dilihat**:
1. **Banner resmi Harfest 2026** (`public/images/brand/harfest-banner.png`, kolaborasi dengan Jendela Creative Corps). Ini adalah acuan utama untuk logo, warna, pola, dan maskot.
2. **Screenshot feed Instagram** (`docs/reference/harfest-ig.jpeg`). Ini acuan untuk gaya layout, frame foto band, dan label harga.

> Aturan emas: **jangan menggambar ulang atau mengetik ulang aset resmi.** Logo, lettering HARFEST, dan maskot dipakai sebagai file gambar, bukan dibuat dengan font atau SVG karangan sendiri.

### 11.1 Aset resmi

| Aset | Lokasi | Keterangan |
|---|---|---|
| Banner resmi | `public/images/brand/harfest-banner.png` | Logo + 2 maskot + tagline di atas background magenta. Dipakai sebagai visual hero. |
| Logo (plat kuning + tulisan) | `public/images/brand/harfest-logo.png` (atau `.svg`) | Crop/versi transparan dari banner. Untuk navbar dan footer. |
| Maskot globe | `public/images/mascot/globe.png` | Bola biru berheadphone kuning-pink, sarung tangan putih, sepatu pink. |
| Maskot music player | `public/images/mascot/player.png` | Walkman/music player pink dengan wajah kuning. |
| Aksen kilat | `public/images/brand/burst-yellow.svg`, `burst-blue.svg` | Garis kilat kecil di sekitar logo. |

Jika versi transparan belum tersedia, gunakan banner apa adanya sebagai hero (lihat 11.7) dan tandai di README bahwa aset transparan menyusul. Nama kredit "In collaboration with Jendela Creative Corps" sudah ada di banner, tidak perlu diulang di tempat lain selain footer.

### 11.2 Karakter visual
Playful, ceria, energik, ala poster konser kampus anak muda. Tebal, kontras tinggi, penuh warna dan maskot. **Background dominan magenta**, bukan ungu gelap. Ungu dipakai sebagai warna teks gelap, outline, dan bayangan. **Bukan** desain korporat yang minimalis.

### 11.3 Palet warna (diambil dari banner resmi)

| Token | Hex | Pemakaian |
|---|---|---|
| `--hf-magenta` | `#9A0474` | **Background utama halaman** |
| `--hf-magenta-light` | `#9E0A7B` | Warna pola zigzag (tone-on-tone di atas magenta) |
| `--hf-pink` | `#DE037A` | Aksen, tombol sekunder, kata "HAR", badge |
| `--hf-purple` | `#4D0570` | Teks gelap di atas kuning, outline, hard shadow, kata "FEST", section gelap |
| `--hf-purple-deep` | `#2D0A4E` | Footer, area paling gelap, hard shadow tebal |
| `--hf-yellow` | `#FFD000` | Tombol utama, plat judul, highlight, label harga |
| `--hf-yellow-dark` | `#E6B800` | Hover tombol kuning |
| `--hf-blue` | `#3DB8F0` | Aksen kecil (kilat biru, detail maskot) |
| `--hf-white` | `#FFFFFF` | Teks isi di atas magenta/ungu |

Kombinasi khas: **plat kuning tidak beraturan dengan outline ungu**, berisi teks ungu atau pink. Teks putih/kuning di atas magenta. Pola zigzag sangat halus di background.

### 11.4 Pola background
- **Zigzag siku tebal bergaya labirin**, **satu warna** dan hanya beda tipis dari background: `#9E0A7B` di atas `#9A0474`.
- Dibuat sebagai SVG yang bisa di-tile (`public/images/patterns/zigzag.svg`) dan class `.bg-hf-pattern`.
- Efeknya harus **subtle**, seperti background banner resmi. Dilarang memakai pola kotak diagonal (lattice) berkontras tinggi.
- Di feed IG, band pola juga muncul sebagai pita dekoratif pink di atas/bawah section. Boleh dipakai sebagai `pattern-divider` dengan warna `--hf-pink` di atas `--hf-purple`.

### 11.5 Tipografi
- **Logo/lettering HARFEST**: hanya dari file gambar resmi. **Tidak boleh diketik dengan font.**
- **Isi, UI, tagline**: **Montserrat** (500, 600, 700, 800). Meniru tagline "Harkat Negeri Fest 2026" pada banner.
- **Judul section**: Montserrat ExtraBold/Black, huruf kapital, warna ungu `--hf-purple` di atas plat kuning tidak beraturan, atau teks kuning dengan outline ungu 2-3px.
- Opsional untuk aksen display (angka harga besar seperti "30K"): Montserrat Black atau "Bowlby One".

### 11.6 Elemen khas yang harus ada
1. **Plat kuning tidak beraturan** (bentuk seperti kertas sobek/stiker) dengan outline ungu tebal, dipakai di belakang judul section dan tagline. Buat sebagai SVG/clip-path yang bisa dipakai ulang di komponen `section-title`.
2. **Maskot asli** (globe biru dan music player pink) di tepi hero dan section, tidak menutupi teks, dengan animasi float halus.
3. **Garis kilat kecil** kuning dan biru di sekitar logo/maskot, seperti pada banner.
4. **Frame foto band**: foto hitam-putih (grayscale) dalam bingkai tebal pink/ungu bersudut membulat, nama band besar di bawah foto (meniru feed IG).
5. **Label harga**: pill/bar kuning dengan teks ungu tebal ("Tiket Umum 1 orang **40K**") dan bar pink untuk variasi ("Civitas Harkat Negeri **30K**").
6. **Hard shadow dan border tebal**: card `rounded-3xl`, border 3-4px ungu, bayangan keras tanpa blur (`shadow-[6px_6px_0_#2D0A4E]`).
7. **Tombol utama**: kuning, teks ungu tebal, border ungu, hard shadow, efek "tekan" saat active (translate 2px).
8. Elemen sedikit miring (rotate -2 sampai 2 derajat) untuk kesan sticker pada badge dan judul.

### 11.7 Layout dan komponen
- **Navbar**: logo resmi di kiri, menu (Beranda, Lineup, Tiket, Pesanan), tombol Masuk dan "Beli Tiket" (kuning). Mobile: hamburger. Background navbar `--hf-purple-deep` atau magenta dengan border bawah kuning.
- **Hero**: banner resmi sebagai visual utama (full width, `object-cover`, tinggi menyesuaikan). Di bawahnya lanjut background `--hf-magenta` yang menyatu, lalu tanggal, lokasi, tombol Beli Tiket (kuning) dan Lineup (outline kuning), serta pill harga.
- **Selang-seling section**: magenta (dominan) dan ungu `--hf-purple` untuk variasi, dipisahkan pita zigzag pink seperti di feed IG.
- **Lineup**: grid frame foto band (2 kolom di mobile, 3-4 di desktop).
- **Tiket**: 3 card (Civitas, Umum 1 Orang, Umum 3 Orang) dengan label harga besar, badge "Sold Out" bila kuota habis.
- **Badge status pesanan**: kuning = Menunggu Pembayaran, biru = Menunggu Verifikasi, hijau = Lunas, merah = Ditolak, abu = Kedaluwarsa/Dibatalkan.
- **E-tiket**: desain seperti tiket konser (potongan lengkung di sisi, garis putus-putus, QR di kanan), memakai logo resmi dan warna Harfest.
- **Panel admin**: tetap memakai palet Harfest tapi lebih **rapi dan padat** (tabel jelas, kontras tinggi, background lebih netral/ungu gelap). Playful secukupnya, prioritas keterbacaan.
- **Animasi**: ringan saja (float maskot, hover tombol). Hormati `prefers-reduced-motion`.

### 11.8 Checklist kemiripan desain
Desain dianggap sesuai jika:
- [ ] Logo di navbar dan hero adalah **gambar resmi**, bukan teks berfont.
- [ ] Background dominan **magenta** `#9A0474` dengan pola zigzag halus.
- [ ] Maskot yang tampil adalah **globe biru berheadphone** dan **music player pink**.
- [ ] Ada plat kuning dengan outline ungu di judul section.
- [ ] Font isi **Montserrat**.
- [ ] Label harga kuning/pink, frame band hitam-putih, hard shadow, dan border tebal terlihat konsisten.
- [ ] Teks tetap terbaca penuh di lebar 375px.

### 11.9 Tambahan desain untuk multi-edisi (referensi poster Vol 1)
- **Section "Bintang Tamu Utama"** di landing: foto headliner berbentuk cutout dengan outline kuning dan pink seperti stiker, maskot globe di satu sisi dan music player di sisi lain, teks nama headliner besar bergaya stiker. Jika headliner belum diumumkan, tampilkan kartu teka-teki "Akan diumumkan" dengan maskot.
- **Lineup mode logo**: kotak berwarna selang-seling kuning dan pink berisi logo band (seperti poster Vol 1). Jika band tidak punya logo, pakai mode foto atau teks.
- **Badge status event**: "Dijual" (kuning), "Segera" (biru), "Selesai" (abu/ungu).
- **Kartu event** di `/events`: poster/logo, nama edisi, tanggal, badge status.
- **Pita pola bawah**: motif zigzag/hati lebih rapat berwarna pink di atas ungu sebagai pemisah section atau footer.
- Logo Vol 1 (tulisan "vol.1") dan Vol 2 bisa berbeda, karena itu logo diatur per event (FR-29).

## 12. Acceptance Criteria Global (Definition of Done)

- [ ] Semua FR berprioritas **M** berfungsi end-to-end.
- [ ] `php artisan migrate:fresh --seed` berjalan tanpa error dan menghasilkan data demo.
- [ ] Alur lengkap bisa didemokan: daftar, pesan, upload bukti, admin terima, e-tiket tampil, check-in sukses, check-in ulang ditolak.
- [ ] Kuota tidak bisa oversell (diuji dengan 2 pesanan bersamaan pada kuota tersisa 1).
- [ ] User A tidak bisa membuka pesanan/bukti bayar user B (403/404).
- [ ] Tampilan lolos **Checklist kemiripan desain (11.8)**: memakai aset resmi Harfest, palet magenta/ungu/kuning, dan nyaman di layar 375px.
- [ ] README berisi cara install, akun demo, dan cara menjalankan.

- [ ] Vol 1 tampil sebagai arsip (Selesai) dengan Enau sebagai headliner dan tidak bisa dibeli.
- [ ] Vol 2 aktif di landing. Pembelian di Vol 2 tidak mengubah kuota Vol 1.
- [ ] Tiket Vol 1 ditolak saat check-in di Vol 2 dan sebaliknya.
- [ ] Event switcher admin mengubah isi dashboard, verifikasi, pesanan, dan CSV.
- [ ] Fitur Duplikat Event menghasilkan event draft dengan jenis tiket yang sama.

## 13. Akun Demo (seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@harfest.test | password |
| User | user@harfest.test | password |

## 14. Risiko dan Asumsi

- Pembayaran manual berarti verifikasi bergantung pada admin. Mitigasi: halaman verifikasi dibuat cepat dipakai.
- Tidak ada integrasi bank. Nomor rekening/QRIS ditaruh di konfigurasi (`config/harfest.php` / `.env`) agar mudah diganti.
- Aset transparan (logo dan maskot) mungkin belum tersedia. Sementara gunakan banner resmi apa adanya sebagai hero. **Jangan membuat ulang logo/maskot** dengan font atau SVG karangan sendiri.
- Data band selain empat yang disebut di atas bersifat placeholder.
- Data Vol 1 di database adalah **dummy**. Jangan memasukkan data pembeli asli (nomor WA, bukti transfer) ke proyek tugas kuliah.
- Nama band ke-6 pada poster Vol 1 belum terkonfirmasi, isi setelah dicek ke akun IG panitia.
- Tanggal Vol 1 dan Vol 2 perlu dikonfirmasi ke panitia, nilai di seeder hanya sementara.
