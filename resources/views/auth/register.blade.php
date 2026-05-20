@extends('layouts.auth')

@section('title', 'Daftar — Si Jaring Nusantara')
@section('auth-badge', '⌘ DAFTAR')
@section('auth-title', 'Buat akun baru.')
@section('auth-lead', 'Daftar untuk mulai berbelanja perangkat jaringan.')
@section('auth-header-link')
  Sudah punya akun? <a href="{{ route('login') }}">Masuk →</a>
@endsection

@section('content')
<form method="POST" action="{{ route('register.post') }}" id="registerForm" novalidate>
  @csrf

  @if ($errors->any())
    <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <div class="field">
    <label for="name">Nama Lengkap</label>
    <input id="name" name="name" type="text" required autocomplete="name"
           value="{{ old('name') }}" placeholder="Budi Santoso"/>
  </div>

  <div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autocomplete="email"
           value="{{ old('email') }}" placeholder="nama@email.com"/>
  </div>

  <div class="field">
    <label for="phone">No. WhatsApp</label>
    <input id="phone" name="phone" type="tel" required autocomplete="tel"
           value="{{ old('phone') }}" placeholder="+62 8xx xxxx xxxx"/>
  </div>

  <div class="field">
    <label for="password">Kata Sandi</label>
    <input id="password" name="password" type="password" required autocomplete="new-password"
           minlength="6" placeholder="minimal 6 karakter"/>
  </div>

  <div class="field">
    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
    <input id="password_confirmation" name="password_confirmation" type="password" required
           autocomplete="new-password" placeholder="ulangi kata sandi"/>
  </div>

  <button type="submit" class="btn-primary auth-submit">Daftar &rarr;</button>
</form>

<p class="auth-switch">
  Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
</p>
@endsection
