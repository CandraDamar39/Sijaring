# CV. Si Jaring Nusantara — Aplikasi Web Dinamis

Aplikasi web **distributor B2B alat jaringan** (router, switch, access point, kabel,
server) untuk tugas **UAS Pemrograman Berbasis Web (KSI1412)** — Universitas Jember.
Dibangun dengan **Laravel 11 + MySQL**, menerapkan HTML5 semantik, CSS, JavaScript
(DOM + validasi), CRUD database, autentikasi session/cookie, komunikasi asinkronus
(AJAX/Fetch + JSON), dan **payment gateway Midtrans (sandbox)**.

---

## Pemenuhan Kriteria Tugas

| Kriteria UAS | Implementasi di proyek | Lokasi kode |
|--------------|------------------------|-------------|
| **HTML & CSS** | HTML5 semantik, design system CSS custom (responsif), dark mode | `resources/views/**`, `public/css/app.css` |
| **JavaScript & DOM** | Cart drawer, modal checkout, validasi form sisi klien, manipulasi DOM | `public/js/app.js` |
| **PHP & CRUD** | CRUD Produk, User, Order (admin); Eloquent ORM + migration + seeder | `app/Http/Controllers/AdminController.php`, `app/Models/**` |
| **Cookies & Session** | Login/logout berbasis session, middleware proteksi role, preferensi via cookie | `app/Http/Middleware/CekLogin.php`, `CekAdmin.php`, `PreferensiController.php` |
| **AJAX / JSON** | Live search produk, **Speed Test jaringan** (ping/download/upload), checkout, preferensi | `KatalogController@ajaxSearch`, `SpeedtestController`, `CheckoutController` |
| **Payment Gateway** | Midtrans Snap (sandbox) + webhook notifikasi terverifikasi signature | `CheckoutController`, `MidtransController` |

---

## Tech Stack

- **Laravel 11** · PHP 8.3 · MySQL 8.4
- Blade templating · Eloquent ORM (local scope, `belongsToMany`) · Migration + Seeder
- **Vanilla CSS** — tanpa Bootstrap / Tailwind / framework JS
- Autentikasi: session + custom middleware `cek.login` & `cek.admin` (role admin vs pelanggan vs tamu)
- Pembayaran: **Midtrans Snap** (mode sandbox), HTTP client Laravel (tanpa SDK eksternal)

---

## Cara Instalasi & Menjalankan (Lokal)

> Prasyarat: PHP 8.2+, Composer, MySQL (mis. via **Laragon** / XAMPP).

```bash
# 1. Masuk folder & install dependency
cd laravel
composer install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate

# 3. Sesuaikan koneksi DB di .env (default: database sijaring_db, user root, tanpa password)
#    Buat database 'sijaring_db' lebih dulu di phpMyAdmin / MySQL.

# 4. Migrasi + isi data contoh
php artisan migrate --seed

# 5. Jalankan
php artisan serve
```

Buka **http://127.0.0.1:8000**

> **Email reset password** dikirim via queue (background). Agar email benar-benar
> terkirim, jalankan worker di **terminal kedua**:
> ```bash
> php artisan queue:work
> ```

### Akun Demo
| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@sijaring.id` | `admin123` |
| Pelanggan | `budi@example.com` | `budi123` |

Atau daftar akun baru via `/register`.

---

## Mengaktifkan Pembayaran Midtrans (Sandbox)

Checkout tetap berfungsi tanpa Midtrans (fallback konfirmasi via WhatsApp). Untuk
mengaktifkan popup pembayaran:

1. Daftar gratis di **https://dashboard.sandbox.midtrans.com** (mode sandbox — tanpa uang asli).
2. Buka **Settings → Access Keys**, salin **Server Key** & **Client Key**.
3. Isi di `.env`:
   ```env
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```
4. `php artisan config:clear`, lalu lakukan checkout — popup Snap akan muncul.
5. Uji bayar dengan **kartu sandbox**: `4811 1111 1111 1114`, CVV `123`, exp `01/26`, OTP `112233`.
6. (Opsional, untuk update status otomatis) set **Payment Notification URL** di dashboard
   Midtrans ke `https://<domain-anda>/midtrans/notification`.

> Langkah demo transaksi lengkap (kartu, VA, QRIS, GoPay) ada di **[PANDUAN-MIDTRANS.md](PANDUAN-MIDTRANS.md)**.

---

## Ringkasan Skema Database (ERD)

- **users** (1) ─< **orders** (N) ─< **order_items** (N) >─ **produks**
- **produks** (N) >─< **kategoris** (N) lewat pivot **produk_kategori** (`belongsToMany`)
- **inquiries** (Network Builder), **contact_settings** (kontak yang dikelola admin)

Kolom kunci `orders`: `order_id`, `payment_method`, `subtotal/shipping/total`,
`status` (fulfillment), `payment_status` (pembayaran), `snap_token` (Midtrans).

---

## Deploy

Panduan deploy ke Railway tersedia di **[PANDUAN-DEPLOY.md](PANDUAN-DEPLOY.md)**.

## Struktur Fitur

**Pelanggan:** katalog + live search, Network Builder (rekomendasi), cart + checkout
(Midtrans), riwayat pesanan, profil 3-tab, preferensi tampilan (dark mode/ukuran font).
**Admin:** dashboard statistik, CRUD produk, kelola pelanggan & pesanan, pengaturan kontak.
