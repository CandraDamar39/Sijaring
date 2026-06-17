@extends('layouts.auth')

@section('title', 'Lupa Sandi — Si Jaring Nusantara')
@section('auth-badge', '⌘ RESET')
@section('auth-title', 'Lupa kata sandi?')
@section('auth-lead', 'Masukkan email akun Anda. Kami kirim kode verifikasi 6 digit untuk membuat sandi baru.')
@section('auth-header-link')
  Ingat sandi Anda? <a href="{{ route('login') }}">Masuk →</a>
@endsection

@section('content')
<form method="POST" action="{{ route('password.email') }}" novalidate>
  @csrf

  @if ($errors->any())
    <div class="error-msg">{{ $errors->first() }}</div>
  @endif

  <div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autocomplete="email"
           value="{{ old('email') }}" placeholder="nama@email.com"/>
  </div>

  <button type="submit" class="btn-primary auth-submit">Kirim Kode Verifikasi &rarr;</button>
</form>

<p class="auth-switch">
  <a href="{{ route('login') }}">&larr; Kembali ke login</a>
</p>
@endsection
