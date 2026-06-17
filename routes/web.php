<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PreferensiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\SpeedtestController;
use Illuminate\Support\Facades\Route;

/* ---------- Public ---------- */
Route::get('/',         [DashboardController::class, 'index'])->name('home');
Route::get('/katalog',  [KatalogController::class,   'index'])->name('katalog');

/* ---------- Praktik Individu P1 — Feature 2: Live search produk (AJAX) ---------- */
Route::get('/api/search-produk', [KatalogController::class, 'ajaxSearch'])->name('api.search-produk');

/* ---------- Praktik Individu P1 — Feature 1: Speed Test jaringan (AJAX async ke server) ---------- */
Route::get('/api/speedtest/ping',     [SpeedtestController::class, 'ping'])->name('speedtest.ping');
Route::get('/api/speedtest/download', [SpeedtestController::class, 'download'])->name('speedtest.download');
Route::post('/api/speedtest/upload',  [SpeedtestController::class, 'upload'])->name('speedtest.upload');

/* ---------- Praktik Individu P1 — Feature 4: Reset hitungan kunjungan (session) ---------- */
Route::post('/katalog/reset-visits', [KatalogController::class, 'resetVisits'])->name('katalog.reset-visits');

/* ---------- Praktik Individu P1 — Feature 3: Preferensi tampilan (cookie) ---------- */
Route::get('/preferensi',  [PreferensiController::class, 'show'])->name('preferensi');
Route::post('/preferensi', [PreferensiController::class, 'save'])->name('preferensi.save');
Route::get('/builder',  [BuilderController::class,   'index'])->name('builder');
Route::post('/builder', [BuilderController::class,   'store'])->name('builder.store');
Route::post('/builder/inquiry', [BuilderController::class, 'inquiry'])->name('builder.inquiry');
Route::view('/tentang', 'tentang')->name('tentang');

/* ---------- Latihan route parameter (Aktivitas Mandiri 2 No. 8) ---------- */
Route::get('/hitung/{a}/{b}', fn ($a, $b) => $a + $b);

Route::get('/kontak',   [KontakController::class, 'show'])->name('kontak');
Route::post('/kontak',  [KontakController::class, 'send'])->name('kontak.send');

/* ---------- Keranjang (rekomendasi dari builder) ---------- */
Route::get('/keranjang',         [KeranjangController::class, 'index'])->name('keranjang');
Route::post('/keranjang/clear',  [KeranjangController::class, 'clear'])->name('keranjang.clear');

/* ---------- Checkout (public, with cart payload) ---------- */
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout');

/* ---------- Midtrans webhook (server-to-server, dikecualikan dari CSRF) ---------- */
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

/* ---------- Midtrans cek status manual/otomatis (Status API, tanpa webhook) ---------- */
Route::post('/midtrans/sync-status/{orderId}', [MidtransController::class, 'syncStatus'])->name('midtrans.sync');

/* ---------- Auth (guest only) ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    /* Lupa sandi — kode verifikasi via email */
    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showRequest'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendCode'])->name('password.email');
    Route::get('/reset-password',   [ForgotPasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password',  [ForgotPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('cek.login')->name('logout');

/* ---------- Riwayat (login required) ---------- */
Route::get('/riwayat', [RiwayatController::class, 'index'])
    ->middleware('cek.login')->name('riwayat');

/* ---------- Profil (login required) ---------- */
Route::middleware('cek.login')->group(function () {
    Route::get('/profil',           [ProfilController::class, 'show'])->name('profil');
    Route::post('/profil/profile',  [ProfilController::class, 'updateProfile'])->name('profil.profile');
    Route::post('/profil/address',  [ProfilController::class, 'updateAddress'])->name('profil.address');
    Route::post('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');
    Route::delete('/profil',        [ProfilController::class, 'destroy'])->name('profil.destroy');
});

/* ---------- Admin (login + admin only) ---------- */
Route::prefix('admin')->name('admin.')->middleware(['cek.login', 'cek.admin'])->group(function () {
    Route::get('/',        [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',   [AdminController::class, 'users'])->name('users');
    Route::get('/produk',  [AdminController::class, 'produk'])->name('produk');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');

    Route::post('/produk',                 [AdminController::class, 'storeProduk'])->name('produk.store');
    Route::patch('/produk/{produk}',       [AdminController::class, 'updateProduk'])->name('produk.update');
    Route::delete('/produk/{produk}',      [AdminController::class, 'destroyProduk'])->name('produk.destroy');

    Route::delete('/users/{user}',         [AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/settings/contact',  [AdminController::class, 'contactSettings'])->name('contact.settings');
    Route::post('/settings/contact', [AdminController::class, 'updateContactSettings'])->name('contact.update');
});
