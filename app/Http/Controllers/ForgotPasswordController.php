<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

// FEATURE-ID: AUTH_SYSTEM (FORGOT PASSWORD)
// TAG: AUTH_SYSTEM
// AUTH FEATURE: RESET PASSWORD via KODE 6 DIGIT (email) | TABLE: password_reset_tokens | TTL: 15 menit
/**
 * Reset kata sandi via KODE VERIFIKASI 6 digit yang dikirim ke email.
 * Kode disimpan ter-hash di tabel bawaan `password_reset_tokens` (berlaku 15 menit).
 */
class ForgotPasswordController extends Controller
{
    /** Form minta kode (input email). */
    public function showRequest()
    {
        return view('auth.forgot-password');
    }

    // AUTH FEATURE: KIRIM KODE RESET (queue email)
    // TAG: AUTH_SYSTEM | Mail::raw via dispatch() | kode di-Hash::make()
    /** Buat kode 6 digit, simpan ter-hash, kirim ke email. */
    public function sendCode(Request $request)
    {
        $request->validate(
            ['email' => 'required|email|exists:users,email'],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email'    => 'Format email tidak valid.',
                'email.exists'   => 'Email tidak terdaftar.',
            ]
        );

        $email = $request->email;
        $code  = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        // Kirim email kode verifikasi.
        //
        // PENTING: di Railway QUEUE_CONNECTION=sync, jadi pengiriman terjadi
        // langsung di dalam request (bukan background). Pengiriman Gmail SMTP
        // bisa lambat/gagal di server. Bungkus dalam try/catch agar kegagalan
        // SMTP TIDAK membuat halaman 500 — token sudah tersimpan dan user
        // tetap diarahkan ke form verifikasi.
        try {
            Mail::raw(
                "Kode verifikasi reset password Anda: {$code}\n\n" .
                "Masukkan kode ini untuk membuat kata sandi baru. Berlaku 15 menit.\n" .
                "Abaikan email ini jika Anda tidak memintanya.\n\n— CV. Si Jaring Nusantara",
                function ($m) use ($email) {
                    $m->to($email)->subject('Kode Reset Password — Si Jaring Nusantara');
                }
            );
            session()->flash('success', "Kode verifikasi sedang dikirim ke {$email}. Cek inbox (dan folder spam) dalam beberapa detik.");
        } catch (\Throwable $e) {
            report($e); // dicatat ke log, tidak ditampilkan ke user
            session()->flash('success', "Permintaan reset untuk {$email} sudah diproses. Jika kode tidak masuk dalam 1 menit, coba kirim ulang atau hubungi admin.");
        }

        return redirect()->route('password.reset', ['email' => $email]);
    }

    /** Form verifikasi kode + kata sandi baru. */
    public function showReset(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->query('email', '')]);
    }

    // AUTH FEATURE: VERIFIKASI KODE + UPDATE PASSWORD
    // TAG: AUTH_SYSTEM | cek expired 15 menit + Hash::check(code)
    /** Verifikasi kode lalu perbarui kata sandi. */
    public function reset(Request $request)
    {
        $data = $request->validate(
            [
                'email'    => 'required|email|exists:users,email',
                'code'     => 'required|digits:6',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'code.required'      => 'Kode verifikasi wajib diisi.',
                'code.digits'        => 'Kode verifikasi harus 6 digit.',
                'password.required'  => 'Kata sandi baru wajib diisi.',
                'password.min'       => 'Kata sandi minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
                'email.exists'       => 'Email tidak terdaftar.',
            ]
        );

        $row = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (! $row) {
            return back()->withErrors(['code' => 'Belum ada permintaan reset untuk email ini.'])->withInput();
        }
        if (Carbon::parse($row->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

            return back()->withErrors(['code' => 'Kode sudah kedaluwarsa. Silakan minta kode baru.'])->withInput();
        }
        if (! Hash::check($data['code'], $row->token)) {
            return back()->withErrors(['code' => 'Kode verifikasi salah.'])->withInput();
        }

        // Perbarui kata sandi (auto-hash lewat cast 'hashed' pada model User).
        User::where('email', $data['email'])->first()->update(['password' => $data['password']]);

        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        session()->flash('success', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru.');

        return redirect()->route('login');
    }
}
