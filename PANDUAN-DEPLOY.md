# Panduan Deploy ke Railway — Si Jaring (Laravel + MySQL)

Railway dipilih karena paling mudah untuk Laravel + MySQL: deploy langsung dari GitHub,
database MySQL sekali klik, dan domain publik yang stabil (tidak "tidur"). Estimasi: 15–20 menit.

> **Prasyarat:** kode sudah ada di GitHub (folder `laravel` sebagai root repository),
> akun GitHub, dan akun Railway (daftar gratis via GitHub di c).

---

## Langkah 1 — Siapkan APP_KEY

Di lokal, jalankan dan **catat** hasilnya (akan dipakai di Langkah 4):
```bash
php artisan key:generate --show
```
Contoh hasil: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=`

## Langkah 2 — Buat Project dari GitHub

1. Railway → **New Project** → **Deploy from GitHub repo** → pilih repo Si Jaring.
2. Jika repo Anda berisi folder `laravel` di dalam folder lain, set **Settings → Root Directory** = `laravel`.
3. Railway otomatis mendeteksi PHP/Composer (Nixpacks) dan menjalankan `composer install`.

## Langkah 3 — Tambah Database MySQL

1. Di canvas project → **+ New** → **Database** → **Add MySQL**.
2. Railway membuat service MySQL beserta variabel koneksi (`MYSQLHOST`, `MYSQLPORT`, dst).

## Langkah 4 — Set Environment Variables

Buka service aplikasi → tab **Variables** → tambahkan:

```env
APP_NAME=SiJaring Nusantara
APP_ENV=production
APP_KEY=base64:...        # tempel hasil Langkah 1
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

# Koneksi DB — pakai referensi variabel dari service MySQL Railway:
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Midtrans (sandbox) — salin nilai dari .env lokal
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false

# Email (Gmail SMTP) — untuk fitur reset password; salin dari .env lokal
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=app-password-gmail-16-digit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=emailkamu@gmail.com
```

> Ketik `${{MySQL.MYSQLHOST}}` persis seperti itu — Railway menggantinya otomatis
> (sesuaikan `MySQL` dengan nama service database Anda).

## Langkah 5 — Start Command (migrasi + seed + server)

Service aplikasi → **Settings → Deploy → Custom Start Command**:
```bash
php artisan migrate --force && php artisan db:seed --force && php artisan serve --host 0.0.0.0 --port $PORT
```
- `migrate --force` membuat tabel; `db:seed --force` mengisi akun demo + produk.
- Seeder memakai `firstOrCreate`/`updateOrCreate` → **aman** dijalankan berulang (idempotent),
  jadi boleh tetap di Start Command tanpa menduplikasi data.
- `$PORT` disediakan Railway; `--host 0.0.0.0` agar bisa diakses publik.

## Langkah 6 — Generate Domain

**Settings → Networking → Generate Domain** → muncul URL publik
(mis. `https://sijaring-production.up.railway.app`). Data awal (akun demo + produk)
sudah otomatis terisi oleh `db:seed --force` di Start Command (Langkah 5).

## Langkah 7 — Set Webhook Midtrans

Agar status pembayaran ter-update otomatis:
1. Dashboard Midtrans (sandbox) → **Settings → Configuration**.
2. **Payment Notification URL** = `https://<domain-railway-anda>/midtrans/notification`
3. Simpan. Endpoint ini sudah dikecualikan dari CSRF & memverifikasi signature.

---

## ✅ Checklist Sebelum Demo

- [ ] Buka `https://<domain>` → homepage tampil
- [ ] Login admin (`admin@sijaring.id` / `admin123`) berhasil
- [ ] Katalog tampil + live search jalan (AJAX)
- [ ] Widget **Speed Test** jalan saat tombol diklik
- [ ] Checkout → popup **Midtrans Snap** muncul → bayar pakai kartu sandbox `4811 1111 1111 1114`
- [ ] Status order ter-update setelah pembayaran (cek di Riwayat / Admin)

## 🛠️ Troubleshooting

| Masalah | Penyebab & Solusi |
|---------|-------------------|
| **500 / "No application encryption key"** | `APP_KEY` belum diset. Ulangi Langkah 1 & 4. |
| **"Access denied for user" / DB error** | Variabel `DB_*` salah. Pastikan pakai referensi `${{MySQL.*}}` dan service MySQL aktif. |
| **Halaman 404 saat refresh** | Pastikan Start Command pakai `php artisan serve` (bukan hanya `public/`). |
| **Popup Midtrans tidak muncul** | `MIDTRANS_CLIENT_KEY` kosong/salah → cek Variables, lalu redeploy. |
| **Status bayar tak berubah** | Webhook belum diset (Langkah 7) atau URL salah. |
| **Tabel kosong (tak ada produk/akun)** | Seed belum dijalankan (Langkah 6). |

---

> **Catatan:** `php artisan serve` cukup untuk keperluan demo UAS. Untuk produksi nyata,
> gunakan Nginx + PHP-FPM. Pastikan `APP_DEBUG=false` agar pesan error tidak bocor.
