<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        // Kirim email di BACKGROUND (queue) agar halaman langsung redirect.
        // Pengiriman Gmail SMTP bisa lambat (10–40 detik); tidak boleh memblokir request.
        // Jalankan worker: `php artisan queue:work` agar email benar-benar terkirim.
        dispatch(function () use ($email, $code) {
            Mail::raw(
                "Kode verifikasi reset password Anda: {$code}\n\n" .
                "Masukkan kode ini untuk membuat kata sandi baru. Berlaku 15 menit.\n" .
                "Abaikan email ini jika Anda tidak memintanya.\n\n— CV. Si Jaring Nusantara",
                function ($m) use ($email) {
                    $m->to($email)->subject('Kode Reset Password — Si Jaring Nusantara');
                }
            );
        });

        session()->flash('success', "Kode verifikasi sedang dikirim ke {$email}. Cek inbox (dan folder spam) dalam beberapa detik.");

        return redirect()->route('password.reset', ['email' => $email]);
    }

    /** Form verifikasi kode + kata sandi baru. */
    public function showReset(Request $request)
    {
        return view('auth.reset-password', ['email' => $request->query('email', '')]);
    }

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
