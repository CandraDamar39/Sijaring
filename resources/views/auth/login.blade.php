@extends('layouts.auth')

@section('title', 'Masuk — Si Jaring Nusantara')
@section('auth-badge', '⌘ LOGIN')
@section('auth-title', 'Masuk ke akun.')
@section('auth-lead', 'Selamat datang kembali — silakan masukkan kredensial Anda.')
@section('auth-header-link')
  Belum punya akun? <a href="{{ route('register') }}">Daftar →</a>
@endsection

@section('content')
<form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
  @csrf

  @if ($errors->any())
    <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autocomplete="email"
           value="{{ old('email') }}" placeholder="nama@email.com"/>
  </div>

  <div class="field">
    <label for="password">Kata Sandi</label>
    <input id="password" name="password" type="password" required autocomplete="current-password"
           placeholder="••••••••"/>
  </div>

  <div class="row">
    <label class="remember">
      <input type="checkbox" name="remember" value="1"/>
      <span>Ingat saya</span>
    </label>
    <a href="#" class="forgot">Lupa sandi?</a>
  </div>

  <button type="submit" class="btn-primary auth-submit">Masuk &rarr;</button>
</form>

<p class="auth-switch">
  Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
</p>

<div class="auth-hint">
  <div class="hint-label">Demo Akun Admin</div>
  <p style="margin:0;font-size:.85rem;">
    Email: <code>admin@sijaring.id</code><br>
    Password: <code>admin123</code>
  </p>
</div>
@endsection
