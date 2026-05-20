@extends('layouts.app')

@section('title', 'Kontak — CV. Si Jaring Nusantara')

@push('page-styles')
<style>
  body { padding-top: 0; }
  .contact-hero {
    background: var(--ink); color: var(--bg);
    padding: 7rem 1.4rem 5rem;
    border-bottom: 2px solid var(--ink);
    position: relative; overflow: hidden;
  }
  .contact-hero::before {
    content: ""; position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.06) 1px, transparent 0);
    background-size: 24px 24px; pointer-events: none;
  }
  .contact-hero .wrap { max-width: var(--container); margin: 0 auto; position: relative; }
  .contact-hero .kbd { background: var(--accent-pink); color: #fff; border-color: transparent; }
  .contact-hero h1 {
    font-family: var(--font-display); font-weight: 800;
    font-size: clamp(2.6rem, 7vw, 5rem); line-height: .96; letter-spacing: -.02em;
    margin: 1rem 0; max-width: 16ch;
  }
  .contact-hero p { color: #c8c2b3; max-width: 560px; font-size: 1.05rem; line-height: 1.55; }

  .contact-grid {
    max-width: var(--container); margin: -3rem auto 4rem;
    display: grid; grid-template-columns: 1fr; gap: 1.5rem;
    padding: 0 1.4rem; position: relative; z-index: 2;
  }
  @media (min-width: 880px) { .contact-grid { grid-template-columns: 1.1fr 1fr; gap: 2rem; } }
  .contact-card {
    background: var(--bg); border: 2px solid var(--ink); border-radius: 16px;
    padding: 1.8rem 1.6rem; box-shadow: 8px 10px 0 0 var(--ink);
  }
  .contact-card h2 { font-family: var(--font-display); font-weight: 800; font-size: 1.6rem; margin-bottom: 1.2rem; }
  .info-list { list-style: none; padding: 0; margin: 0; }
  .info-list li { display: flex; gap: .9rem; align-items: flex-start; padding: 1rem 0; border-bottom: 1px dashed rgba(15,14,12,.18); }
  .info-list li:last-child { border-bottom: none; }
  .info-list .ic {
    flex-shrink: 0; width: 40px; height: 40px;
    background: var(--accent-pink); color: #fff;
    border: 2px solid var(--ink); border-radius: 10px;
    display: grid; place-items: center;
  }
  .info-list .ic.cyan { background: var(--accent-cyan); color: var(--ink); }
  .info-list .ic.yellow { background: var(--accent-yellow); color: var(--ink); }
  .info-list .ic.green { background: var(--accent-green); color: #fff; }
  .info-list .ic svg { width: 20px; height: 20px; }
  .info-list .label { font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
  .info-list .val { font-weight: 600; font-size: 1rem; margin-top: .15rem; line-height: 1.45; }
  .info-list .val a:hover { color: var(--accent-pink); }

  .map-card { padding: 0; overflow: hidden; }
  .map-card iframe { width: 100%; height: 380px; border: none; display: block; }
  .map-card .map-foot {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 1.4rem; border-top: 2px solid var(--ink);
    background: var(--bg-2); flex-wrap: wrap; gap: .8rem;
  }
  .map-card .map-foot strong { font-family: var(--font-mono); font-size: .82rem; }
  .map-card .map-foot a {
    font-family: var(--font-display); font-size: .82rem; letter-spacing: .04em;
    background: var(--ink); color: var(--bg);
    padding: .55rem .9rem; border-radius: 8px; border: 1.5px solid var(--ink);
    transition: all .2s;
  }
  .map-card .map-foot a:hover {
    background: var(--accent-pink); transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 var(--ink);
  }

  .form-card form { display: grid; gap: 1rem; }
  .form-card .field input, .form-card .field textarea, .form-card .field select {
    width: 100%; padding: .8rem .95rem;
    border: 1.5px solid var(--ink); border-radius: 9px;
    background: #fff; font-family: var(--font-body); font-size: 1rem;
  }
  .form-card .field input:focus, .form-card .field textarea:focus, .form-card .field select:focus {
    outline: none; border-color: var(--accent-pink);
    box-shadow: 0 0 0 3px rgba(255,46,126,.18);
  }
  .form-card label { display: block; font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin-bottom: .35rem; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
  @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
  .form-card .btn-primary {
    width: 100%; background: var(--ink); color: var(--bg);
    padding: .9rem 1.2rem; border-radius: 9px;
    font-family: var(--font-display); font-weight: 700; letter-spacing: .03em;
    text-transform: uppercase; font-size: .92rem;
    border: 1.5px solid var(--ink); transition: all .2s;
  }
  .form-card .btn-primary:hover { background: var(--accent-pink); transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 var(--ink); }
  .form-ok {
    background: rgba(31,168,91,.12); border: 1px solid var(--accent-green);
    color: #156d3a; padding: .8rem 1rem; border-radius: 8px;
    font-family: var(--font-mono); font-size: .85rem; margin-bottom: 1rem;
  }

  .hours-list { list-style: none; padding: 0; margin: 0; }
  .hours-list li {
    display: flex; justify-content: space-between;
    padding: .65rem 0; border-bottom: 1px dashed rgba(15,14,12,.15);
    font-family: var(--font-mono); font-size: .9rem;
  }
  .hours-list li:last-child { border-bottom: none; }
  .hours-list .day { font-weight: 600; }
  .hours-list .closed { color: var(--accent-pink); }
  .hours-list .open-now {
    background: var(--accent-green); color: #fff;
    font-size: .65rem; padding: .15rem .4rem; border-radius: 4px;
    margin-left: .5rem; font-weight: 700; letter-spacing: .1em;
  }

  .cta-band {
    background: var(--accent-pink); color: #fff;
    padding: 3rem 1.4rem; margin-bottom: 0;
    border-top: 2px solid var(--ink); border-bottom: 2px solid var(--ink);
  }
  .cta-band .wrap {
    max-width: var(--container); margin: 0 auto;
    display: flex; justify-content: space-between; align-items: center;
    gap: 2rem; flex-wrap: wrap;
  }
  .cta-band h2 {
    font-family: var(--font-display); font-weight: 800;
    font-size: clamp(1.6rem, 4vw, 2.4rem); letter-spacing: -.01em; max-width: 22ch;
  }
  .cta-band a {
    background: var(--ink); color: var(--bg);
    padding: 1rem 1.6rem; border-radius: 10px;
    font-family: var(--font-display); font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; font-size: .9rem;
    border: 2px solid var(--ink); transition: all .2s;
    display: inline-flex; align-items: center; gap: .5rem; white-space: nowrap;
  }
  .cta-band a:hover { background: var(--bg); color: var(--ink); transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 var(--ink); }
</style>
@endpush

@section('content')

<section class="contact-hero">
  <div class="wrap">
    <span class="kbd">⌘ KONTAK.05</span>
    <h1>Mau ngobrol langsung? Mampir aja.</h1>
    <p>Tim kami siap bantu konsultasi konfigurasi, request penawaran khusus, atau garansi unit.
       Datang ke kantor Sumbersari, atau chat dulu via WhatsApp — biasanya kami balas dalam 15 menit di jam kerja.</p>
  </div>
</section>

<main class="contact-grid">

  <article class="contact-card">
    <h2>Hubungi Tim.</h2>
    <ul class="info-list">
      <li>
        <span class="ic green">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-9 8.5 8.5 8.5 0 0 1-7-3.7L3 21l4.7-2a8.5 8.5 0 0 1-1.7-5 8.38 8.38 0 0 1 8.5-9 8.38 8.38 0 0 1 6.5 6.5z"/>
          </svg>
        </span>
        <div>
          <div class="label">WhatsApp / Telepon</div>
          <div class="val"><a href="https://wa.me/{{ $cs['whatsapp_link'] ?? '6282329277901' }}" target="_blank" rel="noopener">{{ $cs['whatsapp'] ?? '+62 823 2927 7901' }}</a></div>
        </div>
      </li>
      <li>
        <span class="ic cyan">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>
          </svg>
        </span>
        <div>
          <div class="label">Email</div>
          <div class="val"><a href="mailto:{{ $cs['email'] ?? 'cs@sijaring.id' }}">{{ $cs['email'] ?? 'cs@sijaring.id' }}</a></div>
        </div>
      </li>
      <li>
        <span class="ic yellow">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
          </svg>
        </span>
        <div>
          <div class="label">Alamat Kantor</div>
          <div class="val">{!! nl2br(e($cs['address'] ?? "Jl. Panjaitan Blok F, Gg. Sebelah Alfamart No. 108,\nRW.26 Lingk. Sadengan, Kebonsari,\nKec. Sumbersari, Kab. Jember, Jawa Timur 68122")) !!}</div>
        </div>
      </li>
      <li>
        <span class="ic">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
          </svg>
        </span>
        <div style="flex:1">
          <div class="label">Jam Operasional</div>
          <ul class="hours-list" id="hoursList">
            <li><span class="day">Senin – Jumat</span><span>{{ $cs['hours_weekday'] ?? '08.00 – 18.00' }}</span></li>
            <li><span class="day">Sabtu</span><span>{{ $cs['hours_saturday'] ?? '09.00 – 15.00' }}</span></li>
            <li><span class="day">Minggu</span><span class="closed">{{ $cs['hours_sunday'] ?? 'Tutup' }}</span></li>
          </ul>
        </div>
      </li>
    </ul>
  </article>

  <article class="contact-card form-card">
    <h2>Kirim Pesan.</h2>
    <p style="color:var(--muted);font-size:.95rem;margin-bottom:1.2rem">
      Punya pertanyaan teknis atau request RFQ? Isi form di bawah — kami balas via email/WA.
    </p>

    @if (session('success'))
      <div class="form-ok">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('kontak.send') }}" novalidate>
      @csrf
      <div class="form-row">
        <div class="field">
          <label for="cName">Nama</label>
          <input id="cName" name="name" required placeholder="Budi Santoso"
                 value="{{ old('name', Auth::user()?->name) }}"/>
        </div>
        <div class="field">
          <label for="cPhone">WhatsApp</label>
          <input id="cPhone" name="phone" required type="tel" placeholder="+62 …"
                 value="{{ old('phone', Auth::user()?->phone) }}"/>
        </div>
      </div>
      <div class="field">
        <label for="cEmail">Email</label>
        <input id="cEmail" name="email" required type="email" placeholder="nama@email.com"
               value="{{ old('email', Auth::user()?->email) }}"/>
      </div>
      <div class="field">
        <label for="cTopic">Topik</label>
        <select id="cTopic" name="topic" required>
          <option value="">— Pilih topik —</option>
          @foreach (['Konsultasi teknis / konfigurasi','Request penawaran (RFQ) besar','Klaim garansi / retur','Status pengiriman','Kemitraan / reseller','Lainnya'] as $t)
            <option value="{{ $t }}" {{ old('topic') === $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="cMsg">Pesan</label>
        <textarea id="cMsg" name="message" rows="5" required
                  placeholder="Ceritakan kebutuhan kamu di sini…">{{ old('message') }}</textarea>
      </div>
      <button type="submit" class="btn-primary">Kirim Pesan &rarr;</button>
    </form>
  </article>

  <article class="contact-card map-card" style="grid-column: 1 / -1;">
    <iframe src="{{ $cs['maps_embed_url'] ?? 'https://www.google.com/maps?q=Jl.+Panjaitan+Sumbersari+Jember&output=embed' }}"
            loading="lazy" title="Peta lokasi Si Jaring Nusantara"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    <div class="map-foot">
      <strong>// Lokasi kantor — buka di Google Maps untuk navigasi</strong>
      <a href="{{ $cs['maps_link'] ?? '#' }}"
         target="_blank" rel="noopener">Buka di Maps &rarr;</a>
    </div>
  </article>

</main>

<section class="cta-band">
  <div class="wrap">
    <h2>Belum nemu yang dicari? Cek katalog dulu.</h2>
    <a href="{{ route('katalog') }}">Lihat Katalog &rarr;</a>
  </div>
</section>

@endsection

@push('scripts')
<script>
  // Auto-open WhatsApp if redirect carries wa_url
  @if (session('wa_url'))
    window.open(@json(session('wa_url')), '_blank', 'noopener');
  @endif

  // Open-now indicator on hours list
  (function () {
    const day  = new Date().getDay();
    const hour = new Date().getHours();
    const lis  = document.querySelectorAll('#hoursList li');
    let idx = -1;
    if (day >= 1 && day <= 5 && hour >= 8 && hour < 18) idx = 0;
    else if (day === 6 && hour >= 9 && hour < 15)        idx = 1;
    if (idx >= 0 && lis[idx]) {
      const tag = document.createElement('span');
      tag.className = 'open-now';
      tag.textContent = 'BUKA';
      lis[idx].querySelector('.day').appendChild(tag);
    }
  })();
</script>
@endpush
