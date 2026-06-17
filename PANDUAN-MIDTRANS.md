# Panduan Demo Transaksi Midtrans (Sandbox)

Cara menyelesaikan transaksi di **mode sandbox** (tanpa uang asli) sampai status
"berhasil/selesai". Pastikan dulu `MIDTRANS_SERVER_KEY` & `MIDTRANS_CLIENT_KEY`
(awalan `SB-Mid-...`) sudah diisi di `.env`, lalu `php artisan config:clear`.

---

## A. Cara TERCEPAT: Kartu Kredit (selesai instan)

1. Tambah produk ke keranjang → **Checkout** → isi data → **Konfirmasi Pesanan**.
2. Popup **Midtrans Snap** muncul → pilih **Card Payment / Kartu Kredit**.
3. Masukkan kartu uji sandbox:
   | Field | Nilai |
   |-------|-------|
   | Nomor kartu | `4811 1111 1111 1114` |
   | CVV | `123` |
   | Exp | bulan/tahun apa pun di masa depan, mis. `12/30` |
   | Nama | bebas |
4. Klik **Pay/Bayar** → muncul halaman **3DS / OTP** → masukkan OTP: **`112233`**.
5. Transaksi langsung **Success** → popup menutup → muncul modal **"Pesanan tercatat"**. ✅

> Kartu uji lain: ditolak `4911 1111 1111 1113` · Mastercard sukses `5211 1111 1111 1117`.

---

## B. Metode lain (VA / QRIS / GoPay / ShopeePay)

Metode non-kartu butuh **Simulator Midtrans** untuk "membayar":
👉 **https://simulator.sandbox.midtrans.com**

1. Di popup Snap, pilih metode (mis. **Bank Transfer / Virtual Account**) → muncul **nomor VA**
   (atau **QR** untuk QRIS, **deeplink** untuk GoPay/ShopeePay).
2. Buka **Simulator Midtrans** → pilih tab sesuai metode (Bank Transfer / QRIS / GoPay …).
3. Masukkan **nomor VA / scan QR** yang tadi muncul → klik **Bayar/Pay**.
4. Status transaksi menjadi **settlement (lunas)**. ✅

---

## C. Agar status pesanan otomatis jadi "Paid" di database

### Cara kerjanya
Saat pembayaran berhasil, **server Midtrans** mengirim notifikasi (HTTP POST) ke
endpoint kita `POST /midtrans/notification`. Controller memverifikasi `signature_key`
lalu mengubah `payment_status` order menjadi `paid` (dan `status` → `Diproses`).

```
[Bayar di Snap] → [Server Midtrans] → POST /midtrans/notification → [verifikasi signature] → update DB
```

Masalahnya: alamat `127.0.0.1:8001` hanya bisa diakses dari komputer Anda — server
Midtrans di internet **tidak bisa menjangkaunya**. Karena itu di localhost butuh
"jembatan" publik (ngrok). Setelah deploy, masalah ini hilang sendiri.

---

### Opsi 1 (paling mudah, tanpa setup): cukup lewat popup
Untuk sekadar mendemokan **alur pembayaran**, Anda tidak wajib update DB. Setelah
bayar, callback `onSuccess` Snap menampilkan modal "Pesanan tercatat" — itu sudah
membuktikan transaksi berhasil. (Status di DB tetap `pending`.)

---

### Opsi 2 (REKOMENDASI untuk localhost): Cek Status via API — **sudah jadi**
Aplikasi ini **bertanya langsung** ke Midtrans (Status API), jadi **tidak perlu** webhook/ngrok
di localhost. Dua cara pakai:

1. **Otomatis** — saat popup pembayaran **sukses**, aplikasi memanggil
   `POST /midtrans/sync-status/{order_id}` → status order langsung diperbarui (mis. ke **paid**).
2. **Manual** — buka halaman **Riwayat** (atau Admin → pesanan). Pada pesanan yang masih
   *Menunggu Pembayaran* tersedia tombol **"↻ Cek Status Bayar"**. Klik → status di-refresh
   dari Midtrans (berguna untuk VA/QRIS yang dibayar belakangan di simulator).

> Untuk video demo: bayar pakai kartu → status otomatis jadi **Lunas**; atau klik
> **"Cek Status Bayar"** di Riwayat untuk menampilkan perubahan status secara live.

---

### Opsi 3 (alternatif): webhook lokal pakai ngrok — LANGKAH RINCI

**C.1 — Daftar & install ngrok**
- Buka **https://ngrok.com** → **Sign up** (gratis) → verifikasi email.
- Download ngrok untuk Windows → ekstrak `ngrok.exe` (mis. ke `C:\ngrok\`).
  (Alternatif via Chocolatey: `choco install ngrok`.)

**C.2 — Pasang authtoken** (sekali saja)
- Di dashboard ngrok → menu **Your Authtoken** → salin token.
- Jalankan di terminal:
  ```bash
  C:\ngrok\ngrok.exe config add-authtoken <TOKEN_ANDA>
  ```

**C.3 — Nyalakan tunnel ke server Laravel (port 8001)**
- Pastikan `php artisan serve --port=8001` sedang jalan.
- Buka terminal baru:
  ```bash
  C:\ngrok\ngrok.exe http 8001
  ```
- ngrok menampilkan baris **Forwarding**, contoh:
  ```
  Forwarding   https://a1b2-103-xx-xx.ngrok-free.app -> http://localhost:8001
  ```
  Salin URL `https://...ngrok-free.app` itu (URL publik Anda).

**C.4 — Daftarkan URL webhook di Midtrans**
- Buka **https://dashboard.sandbox.midtrans.com** → **Settings → Configuration**.
- Isi **Payment Notification URL** dengan URL ngrok + `/midtrans/notification`:
  ```
  https://a1b2-103-xx-xx.ngrok-free.app/midtrans/notification
  ```
- Klik **Update / Save**.

**C.5 — Lakukan pembayaran sandbox**
- Buka aplikasi lewat **URL ngrok** (atau tetap di `127.0.0.1:8001`) → checkout.
- Bayar pakai kartu uji (lihat bagian A) atau Simulator Midtrans (bagian B).

**C.6 — Verifikasi status berubah**
- Buka halaman **Riwayat** (pelanggan) atau **Admin → pesanan**, atau cek DB:
  pesanan tadi `payment_status` = **paid**, `status` = **Diproses**.
- Di jendela ngrok juga terlihat log `POST /midtrans/notification  200 OK`.

> **Catatan ngrok:**
> - URL ngrok **berubah tiap kali dijalankan ulang** (versi gratis) → ulangi C.4 bila restart.
> - Biarkan jendela ngrok tetap terbuka selama demo.
> - Jika status tak berubah, cek jendela ngrok: kalau request masuk tapi balas `403`
>   berarti signature tidak cocok (server key salah); kalau tak ada request sama sekali,
>   Notification URL belum benar.

---

### Di production (Railway) — webhook otomatis
Setelah deploy, set **Payment Notification URL** =
`https://<domain-railway>/midtrans/notification` (lihat `PANDUAN-DEPLOY.md` Langkah 7).
Tidak perlu ngrok karena domain Railway sudah publik.

---

## Ringkas (untuk direkam di video demo)

1. Checkout → popup Snap muncul.
2. Pilih **Kartu** → `4811 1111 1111 1114`, CVV `123`, exp `12/30`, OTP `112233`.
3. Muncul **"Pembayaran berhasil"** → modal konfirmasi pesanan.
4. (Jika sudah deploy/ngrok) buka **Riwayat / Admin** → status pesanan **Paid/Diproses**.
