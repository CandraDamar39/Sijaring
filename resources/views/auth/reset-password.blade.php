@extends('layouts.auth')

@section('title', 'Reset Sandi — Si Jaring Nusantara')
@section('auth-badge', '⌘ VERIFIKASI')
@section('auth-title', 'Buat kata sandi baru.')
@section('auth-lead', 'Masukkan kode 6 digit yang dikirim ke email Anda, lalu kata sandi baru.')
@section('auth-header-link')
  Salah email? <a href="{{ route('password.request') }}">Ulangi →</a>
@endsection

@section('content')
<form method="POST" action="{{ route('password.update') }}" novalidate>
  @csrf

  @if ($errors->any())
    <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autocomplete="email"
           value="{{ old('email', $email) }}" placeholder="nama@email.com"/>
  </div>

  <div class="field">
    <label for="code">Kode Verifikasi (6 digit)</label>
    <input id="code" name="code" type="text" inputmode="numeric" maxlength="6" required
           autocomplete="one-time-code" placeholder="123456"/>
  </div>

  <div class="field">
    <label for="password">Kata Sandi Baru</label>
    <input id="password" name="password" type="password" required autocomplete="new-password"
           minlength="6" placeholder="minimal 6 karakter"/>
  </div>

  <div class="field">
    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
    <input id="password_confirmation" name="password_confirmation" type="password" required
           autocomplete="new-password" placeholder="ulangi kata sandi"/>
  </div>

  <button type="submit" class="btn-primary auth-submit">Simpan Kata Sandi &rarr;</button>
</form>

<p class="auth-switch">
  Belum dapat kode? <a href="{{ route('password.request') }}">Kirim ulang</a>
</p>
@endsection
