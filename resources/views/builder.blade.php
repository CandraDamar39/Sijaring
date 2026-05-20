@extends('layouts.app')

@section('title', 'Network Builder — CV. SiJaring Nusantara')

@php
  $jumlahLabel = [
    'kecil'    => '1 – 25 perangkat (Kantor kecil / lab)',
    'menengah' => '26 – 100 perangkat (Cabang / UKM)',
    'besar'    => '101 – 500 perangkat (Enterprise)',
    'raksasa'  => '500+ perangkat (Data Center)',
  ];
  $skalaLabel = [
    'ruangan' => 'Satu Ruangan / Lab',
    'gedung'  => 'Satu Gedung Bertingkat',
    'kampus'  => 'Antar Gedung (Campus Area)',
    'kota'    => 'Lintas Kota (WAN)',
  ];
  $tipeLabel = ['kabel' => 'Kabel (UTP/Fiber)', 'wireless' => 'Wireless (Wi-Fi)', 'hybrid' => 'Hybrid (Kabel + Wireless)'];
@endphp

@push('page-styles')
<style>
  .builder { max-width: var(--container); margin: 0 auto; padding: 0 1.1rem 5rem; }
  .builder-wrap { background: var(--ink); color: var(--bg); border-radius: var(--radius-lg); padding: 2.4rem 1.6rem; display: grid; gap: 2.4rem; position: relative; overflow: hidden; }
  .builder-wrap::before { content: ""; position: absolute; inset: 0; background-image: repeating-linear-gradient(90deg, rgba(255,255,255,.04) 0 1px, transparent 1px 60px); pointer-events: none; }
  .builder-head .kbd { background: transparent; color: var(--bg); border-color: rgba(255,255,255,.3); }
  .builder-head h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(2rem, 7vw, 3.2rem); margin: .8rem 0 .6rem; line-height: 1; }
  .builder-head p { color: #c9c4b6; max-width: 460px; margin: 0; line-height: 1.6; }
  .builder-form fieldset { border: 1px dashed #4a463c; border-radius: var(--radius); padding: 1.6rem 1.4rem; display: grid; gap: 1.2rem; }
  .builder-form legend { font-family: var(--font-mono); font-size: .75rem; padding: 0 .5rem; color: var(--accent-cyan); letter-spacing: .08em; }
  .bf-field { display: flex; flex-direction: column; gap: .4rem; }
  .bf-field label { font-family: var(--font-mono); font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: #c9c4b6; }
  .bf-field select { appearance: none; -webkit-appearance: none; background: var(--bg); color: var(--ink); border: none; border-radius: 10px; padding: .85rem 1rem; font: inherit; font-weight: 500; cursor: pointer; }
  .builder-form .btn-primary { background: var(--accent-pink); color: #fff; align-self: start; margin-top: .4rem; }
  .builder-form .btn-primary:hover { box-shadow: 6px 6px 0 0 var(--accent-cyan); }

  .alert { display: flex; gap: .8rem; align-items: flex-start; background: rgba(46,204,113,.12); border: 1px solid var(--accent-green); color: #cdeed8; padding: 1rem 1.1rem; border-radius: var(--radius); margin-top: 1.2rem; animation: fadeInUp .35s var(--ease); }
  .alert-icon { width: 28px; height: 28px; border-radius: 50%; background: var(--accent-green); color: var(--ink); display: grid; place-items: center; font-weight: 700; flex-shrink: 0; }
  .alert strong { color: #fff; display: block; margin-bottom: .15rem; }
  .alert p { margin: 0; font-size: .9rem; }

  .rec-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-top: 1.2rem; }
  .rec-card { border: 1.5px dashed #4a463c; border-radius: var(--radius); padding: 1.2rem; display: flex; gap: 1rem; align-items: flex-start; }
  .rec-icon { width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0; display: grid; place-items: center; }
  .rec-icon.pink { background: var(--accent-pink); }
  .rec-icon.cyan { background: var(--accent-cyan); }
  .rec-icon.yellow { background: var(--accent-yellow); }
  .rec-card h4 { font-family: var(--font-body); font-weight: 600; margin: 0 0 .2rem; font-size: .95rem; color: var(--bg); }
  .rec-card p { margin: 0; font-size: .85rem; color: #c9c4b6; line-height: 1.5; }
  .rec-card .rec-link { margin-top: .5rem; display: inline-block; font-family: var(--font-mono); font-size: .75rem; color: var(--accent-cyan); text-decoration: underline; }

  @media (min-width: 768px) { .builder-wrap { grid-template-columns: 1fr 1.1fr; align-items: start; padding: 2.8rem; gap: 3rem; } }
</style>
@endpush

@section('content')

<div class="page-hero">
  <span class="kbd">⌘ TOOL.01</span>
  <h1>Network<br>Builder.</h1>
  <p>Belum tahu mulai dari mana? Pilih skala &amp; jumlah perangkat, biar kami siapkan rekomendasi konfigurasinya.</p>
</div>

<section class="builder">
  <div class="builder-wrap">

    <div>
      <header class="builder-head">
        <span class="kbd">⌘ KONFIGURASI</span>
        <h2>Bangun jaringan<br>tanpa ribet.</h2>
        <p>Isi parameter di bawah dan tim engineer kami akan menyiapkan rekomendasi konfigurasi jaringan.</p>
      </header>

      <form class="builder-form" method="POST" action="{{ route('builder.store') }}" style="margin-top:1.6rem">
        @csrf
        <fieldset>
          <legend>Konfigurasi Awal</legend>

          <div class="bf-field">
            <label for="jumlah">Jumlah Perangkat</label>
            <select id="jumlah" name="jumlah">
              @foreach ($jumlahLabel as $val => $lbl)
                <option value="{{ $val }}" {{ ($old['jumlah'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>

          <div class="bf-field">
            <label for="skala">Skala Area</label>
            <select id="skala" name="skala">
              @foreach ($skalaLabel as $val => $lbl)
                <option value="{{ $val }}" {{ ($old['skala'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>

          <div class="bf-field">
            <label for="tipe">Tipe Koneksi Utama</label>
            <select id="tipe" name="tipe">
              @foreach ($tipeLabel as $val => $lbl)
                <option value="{{ $val }}" {{ ($old['tipe'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>

          <button type="submit" class="btn-primary big">Buat Rekomendasi &rarr;</button>
        </fieldset>

        @if ($show_result)
          <div class="alert" role="status">
            <span class="alert-icon">✓</span>
            <div>
              <strong>Rekomendasi berhasil dibuat!</strong>
              <p>{{ $summary }}</p>
            </div>
          </div>
        @endif
      </form>
    </div>

    @if ($show_result)
      <div>
        <h3 style="font-family:var(--font-display);font-size:1.3rem;margin:0 0 1rem;color:var(--bg);font-weight:400">Rekomendasi Konfigurasi</h3>
        <div class="rec-grid">
          @foreach ($recommendations as $r)
            <div class="rec-card">
              <div class="rec-icon {{ $r['icon'] }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $r['icon'] === 'yellow' ? '#0F0E0C' : '#fff' }}" stroke-width="2.5" stroke-linecap="round">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </div>
              <div>
                <h4>{{ $r['label'] }}</h4>
                <p>{{ $r['desc'] }}</p>
                <a class="rec-link" href="{{ route('katalog') }}">Lihat di katalog →</a>
              </div>
            </div>
          @endforeach
        </div>
        <p style="margin-top:1.4rem;font-family:var(--font-mono);font-size:.75rem;color:#b8b3a4;line-height:1.6">
          // Ini rekomendasi awal. Tim engineer akan menghubungi via WhatsApp untuk menyesuaikan kondisi lapangan.
        </p>
        <a href="https://wa.me/6282329277901" target="_blank" rel="noopener"
           class="btn-primary" style="margin-top:1rem;background:var(--accent-pink);color:#fff">Chat Tim Engineer &rarr;</a>
      </div>
    @else
      <div style="display:flex;align-items:center;justify-content:center;padding:2rem;border:1px dashed #4a463c;border-radius:var(--radius)">
        <p style="color:#888;text-align:center;font-family:var(--font-mono);font-size:.85rem;line-height:1.6">
          // Isi formulir di sebelah kiri,<br>rekomendasi akan muncul di sini.
        </p>
      </div>
    @endif

  </div>
</section>
@endsection
