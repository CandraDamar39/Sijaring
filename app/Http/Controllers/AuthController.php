<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Tampilkan form login. */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'home');
        }
        return view('auth.login');
    }

    /** Proses login. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            session()->flash('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'home');
        }

        return back()->withErrors([
            'email' => '// Email atau kata sandi tidak cocok.',
        ])->withInput($request->only('email'));
    }

    /** Tampilkan form daftar. */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    /** Proses pendaftaran user baru (auto-assign role pelanggan). */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'phone.required'     => 'Nomor WhatsApp wajib diisi.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'pelanggan',
        ]);

        // Arahkan ke halaman login dengan konfirmasi jelas (bukan auto-login),
        // agar pengguna tahu akun berhasil dibuat dan dapat masuk sendiri.
        session()->flash('success', 'Akun berhasil dibuat! Silakan masuk dengan email & kata sandi Anda.');

        return redirect()->route('login');
    }

    /** Logout dan hancurkan session. */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
