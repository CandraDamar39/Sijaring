@extends('layouts.app')

@section('title', 'CV. SiJaring Nusantara — Infrastruktur Jaringan B2B')

@push('page-styles')
<style>
  .hero {
    position: relative;
    max-width: var(--container);
    margin: 0 auto;
    padding: 7rem 1.1rem 3rem;
    overflow: hidden;
  }
  .hero-eyebrow {
    display: inline-flex; align-items: center; gap: .55rem;
    font-family: var(--font-mono); font-size: .72rem;
    background: var(--ink); color: var(--bg);
    padding: .35rem .7rem; border-radius: 999px; letter-spacing: .06em;
  }
  .hero-eyebrow .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); animation: pulse 1.8s infinite; }
  .hero-title {
    font-family: var(--font-display); font-weight: 900;
    font-size: clamp(2.2rem, 9.5vw, 5.4rem); line-height: .96;
    letter-spacing: -.02em; margin: 1.2rem 0 1rem; text-wrap: balance;
  }
  .hero-lead { max-width: 540px; font-size: 1.05rem; color: var(--ink-soft); margin: 0 0 1.6rem; }
  .hero-ctas { display: flex; flex-wrap: wrap; gap: .7rem; }
  .hero-stats {
    margin-top: 2.4rem; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;
    padding-top: 1.4rem; border-top: 1px dashed var(--line); max-width: 540px;
  }
  .hero-stats div { display: flex; flex-direction: column; }
  .hero-stats strong { font-family: var(--font-display); font-size: clamp(1.4rem, 5vw, 2rem); line-height: 1; }
  .hero-stats span { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); margin-top: .35rem; text-transform: uppercase; letter-spacing: .06em; }

  .floating-router {
    position: relative;
    margin: 3rem auto 0;
    width: 320px; height: 280px;
    perspective: 900px;
  }
  .router-shadow {
    position: absolute; left: 50%; bottom: 30px;
    width: 230px; height: 22px;
    background: radial-gradient(ellipse at center, rgba(15,14,12,.42), transparent 70%);
    transform: translateX(-50%); filter: blur(8px);
    animation: shadow-pulse 4s ease-in-out infinite; z-index: 1;
  }
  @keyframes shadow-pulse {
    0%,100% { transform: translateX(-50%) scale(1);   opacity: .65; }
    50%     { transform: translateX(-50%) scale(.85); opacity: .35; }
  }
  .router-photo {
    position: absolute; inset: 0; display: grid; place-items: center; z-index: 2;
    animation: float-switch 4.5s ease-in-out infinite;
    filter: drop-shadow(0 28px 22px rgba(15,14,12,.35));
  }
  @keyframes float-switch {
    0%,100% { transform: translateY(0)     rotate(-2deg); }
    50%     { transform: translateY(-12px) rotate(0deg); }
  }
  .router-photo img { width: 100%; max-width: 420px; height: auto; }
  .router-glow {
    position: absolute; inset: 8% 8%;
    background: radial-gradient(circle at 30% 30%, rgba(0,212,224,.35), transparent 60%);
    filter: blur(40px); z-index: -1;
  }
  .chip-sticker {
    position: absolute; font-family: var(--font-mono); font-size: .68rem;
    padding: .25rem .55rem; border: 1.5px solid var(--ink); border-radius: 6px;
    background: var(--bg); letter-spacing: .08em; z-index: 3;
    animation: bob 5s ease-in-out infinite;
  }
  .chip-sticker.pink { top: 4px; right: 6px; background: var(--accent-pink); color: #fff; transform: rotate(6deg); }
  .chip-sticker.cyan { bottom: 16px; left: 0; transform: rotate(-7deg); animation-delay: -2s; }
  @keyframes bob { 0%,100% { translate: 0 0; } 50% { translate: 0 -6px; } }
  .orbit-line { position: absolute; inset: 0; width: 100%; height: 100%; opacity: .4; animation: spin 30s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }

  .layanan { max-width: var(--container); margin: 0 auto; padding: 4rem 1.1rem; }
  .layanan-grid { display: grid; grid-template-columns: 1fr; gap: 1.2rem; margin-top: 2rem; }
  .layanan-card { background: var(--bg); border: 1.5px solid var(--ink); border-radius: var(--radius); padding: 1.6rem; display: flex; gap: 1.2rem; align-items: flex-start; transition: transform .3s var(--ease), box-shadow .3s var(--ease); }
  .layanan-card:hover { transform: translate(-3px,-6px); box-shadow: var(--shadow-lg); }
  .layanan-icon { width: 48px; height: 48px; flex-shrink: 0; background: var(--ink); border-radius: 12px; display: grid; place-items: center; color: var(--bg); }
  .layanan-icon.pink { background: var(--accent-pink); }
  .layanan-icon.cyan { background: var(--accent-cyan); color: var(--ink); }
  .layanan-icon.yellow { background: var(--accent-yellow); color: var(--ink); }
  .layanan-body h3 { font-family: var(--font-body); font-size: 1.05rem; font-weight: 700; margin: 0 0 .35rem; }
  .layanan-body p { font-size: .9rem; color: var(--muted); margin: 0; line-height: 1.55; }

  .tentang { max-width: var(--container); margin: 0 auto; padding: 4rem 1.1rem; }
  .tentang-wrap { background: var(--ink); color: var(--bg); border-radius: var(--radius-lg); padding: 2.4rem 1.6rem; display: grid; gap: 2rem; position: relative; overflow: hidden; }
  .tentang-wrap::before { content: ""; position: absolute; inset: 0; background-image: repeating-linear-gradient(90deg, rgba(255,255,255,.04) 0 1px, transparent 1px 60px); pointer-events: none; }
  .tentang-wrap .kbd { background: transparent; color: var(--bg); border-color: rgba(255,255,255,.3); }
  .tentang-wrap h2 { font-family: var(--font-display); font-weight: 400; font-size: clamp(1.8rem, 6vw, 3rem); margin: .8rem 0 .6rem; line-height: 1; }
  .tentang-lead { color: #c9c4b6; max-width: 540px; margin: 0 0 1.6rem; line-height: 1.65; }
  .tentang-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; padding-top: 1.4rem; border-top: 1px dashed rgba(255,255,255,.15); }
  .tentang-stats strong { font-family: var(--font-display); font-size: clamp(1.6rem, 5vw, 2.4rem); line-height: 1; color: var(--accent-pink); display: block; }
  .tentang-stats span { font-family: var(--font-mono); font-size: .7rem; color: #b8b3a4; text-transform: uppercase; letter-spacing: .07em; }

  @media (min-width: 768px) {
    .hero { padding: 9rem 1.6rem 4rem; }
    .floating-router { margin-top: 4rem; width: 460px; height: 360px; }
  }
  @media (min-width: 1024px) {
    .hero {
      display: grid;
      grid-template-columns: 1.1fr 1fr;
      column-gap: 3rem;
      align-items: center;
      padding: 10rem 1.6rem 5rem;
      min-height: 88vh;
    }
    .hero-eyebrow { grid-column: 1; grid-row: 1; }
    .hero-title   { grid-column: 1; grid-row: 2; }
    .hero-lead    { grid-column: 1; grid-row: 3; }
    .hero-ctas    { grid-column: 1; grid-row: 4; }
    .hero-stats   { grid-column: 1; grid-row: 5; }
    .floating-router {
      grid-column: 2;
      grid-row: 1 / -1;
      width: 100%; max-width: 480px; height: 440px;
      margin: 0 auto;
    }
    .hero-title { font-size: clamp(3rem, 6.5vw, 5rem); }
    .layanan-grid { grid-template-columns: repeat(2, 1fr); }
    .tentang-wrap { grid-template-columns: 1fr auto; align-items: center; }
    .tentang-stats { grid-template-columns: repeat(4, 1fr); }
  }

  /* ── Weather Section (Feature 1 — wttr.in async) ── */
  .weather-section { max-width: var(--container); margin: 0 auto; padding: 3rem 1.4rem; }
  .weather-wrap { max-width: 700px; }
  .weather-header { margin-bottom: 1.2rem; }
  .weather-sub { color: var(--muted); font-size: .95rem; margin: .5rem 0 0; }
  .weather-card { background: var(--bg); border: 2px solid var(--ink); border-radius: 16px; padding: 1.8rem 1.6rem; box-shadow: 6px 8px 0 0 var(--ink); min-height: 120px; display: flex; align-items: center; }
  .weather-loading { display: flex; align-items: center; gap: 1rem; font-family: var(--font-mono); font-size: .88rem; color: var(--muted); width: 100%; }
  .w-spinner { width: 24px; height: 24px; border: 3px solid var(--line); border-top-color: var(--accent-cyan); border-radius: 50%; animation: spin 0.8s linear infinite; flex-shrink: 0; }
  .weather-data { width: 100%; }
  .weather-main { display: flex; align-items: center; gap: 1.4rem; margin-bottom: 1.4rem; padding-bottom: 1.2rem; border-bottom: 1px dashed var(--line); }
  .w-icon { font-size: 3rem; line-height: 1; }
  .w-city { font-family: var(--font-mono); font-size: .75rem; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); margin-bottom: .3rem; }
  .w-temp { font-family: var(--font-display); font-size: 2.4rem; font-weight: 900; line-height: 1; letter-spacing: -.02em; color: var(--accent-cyan); }
  .w-desc { font-size: .95rem; color: var(--muted); margin-top: .3rem; text-transform: capitalize; }
  .weather-details { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1rem; }
  @media (max-width: 600px) { .weather-details { grid-template-columns: repeat(2,1fr); } }
  .w-detail { display: flex; flex-direction: column; gap: .2rem; }
  .w-label { font-family: var(--font-mono); font-size: .68rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
  .w-val { font-family: var(--font-display); font-size: 1rem; font-weight: 700; }
  .w-note { font-family: var(--font-mono); font-size: .78rem; color: var(--accent-green); padding: .6rem .8rem; background: rgba(46,204,113,.08); border-radius: 8px; border-left: 3px solid var(--accent-green); }
  .w-note:empty { display: none; }
  .weather-error { font-family: var(--font-mono); font-size: .88rem; color: var(--accent-pink); width: 100%; }
  .weather-credit { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); margin-top: .8rem; }
  .weather-credit a { color: var(--accent-cyan); }
</style>
@endpush

@section('content')

<section class="hero" aria-labelledby="hero-title">

  <div class="hero-eyebrow">
    <span class="dot"></span>
    <span>SYS:ONLINE &nbsp;//&nbsp; B2B Network Distributor &nbsp;//&nbsp; EST. 2024</span>
  </div>

  <h1 id="hero-title" class="hero-title">
    INFRASTRUKTUR<br>
    JARINGAN.<br>
    SIAP DEPLOY.
  </h1>

  <p class="hero-lead">
    Distributor B2B alat jaringan enterprise — router, managed switch L2/L3,
    kabel UTP, dan peripheral. Dipercaya ISP, sekolah &amp; kantor di Jember &amp; Jawa Timur.
  </p>

  <div class="hero-ctas">
    <a href="{{ route('katalog') }}" class="btn-primary big">Lihat Katalog</a>
    <a href="{{ route('builder') }}" class="btn-ghost big">Konsultasi gratis</a>
  </div>

  <aside class="hero-stats" aria-label="Statistik singkat">
    <div>
      <strong>{{ \App\Models\Produk::aktif()->sum('stok') }}+</strong>
      <span>stok siap kirim</span>
    </div>
    <div>
      <strong>{{ \App\Models\Produk::aktif()->distinct('kategori')->count('kategori') }}</strong>
      <span>varian kategori</span>
    </div>
    <div>
      <strong>{{ \App\Models\Order::count() }}</strong>
      <span>transaksi sukses</span>
    </div>
  </aside>

  {{-- LAST element: floating router (must be last for grid layout) --}}
  <div class="floating-router" aria-hidden="true">
    <div class="router-shadow"></div>
    <div class="router-photo">
      <img src="{{ asset('assets/router-3d.png') }}" alt="" width="420" height="340">
      <div class="router-glow"></div>
    </div>
    <span class="chip-sticker pink">L3 MANAGED</span>
    <span class="chip-sticker cyan">// 24×GbE + 4×SFP+</span>
    <svg class="orbit-line" viewBox="0 0 240 240" aria-hidden="true">
      <ellipse cx="120" cy="120" rx="115" ry="40" fill="none"
               stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 6"/>
    </svg>
  </div>

</section>

<section class="ticker" aria-label="Brand partner">
  <div class="ticker-track" aria-hidden="true">
    @foreach (['MIKROTIK','CISCO','UBIQUITI','TP-LINK','HUAWEI','D-LINK','MIKROTIK','CISCO','UBIQUITI','TP-LINK','HUAWEI','D-LINK'] as $i => $brand)
      <span>{{ $i % 2 === 0 ? '★ ' : '// ' }}{{ $brand }}</span>
    @endforeach
  </div>
</section>

<section class="layanan" id="layanan">
  <div class="section-head">
    <span class="kbd">⌘ LAYANAN.01</span>
    <h2>Apa yang<br>kami tawarkan.</h2>
  </div>
  @php
    $layanans = [
      ['icon'=>'pink',   'title'=>'Distribusi Perangkat Jaringan',  'desc'=>'Switch L2/L3, router, access point, kabel UTP, SFP+ — stok tersedia, siap kirim ke seluruh Jawa Timur dan luar pulau.'],
      ['icon'=>'cyan',   'title'=>'Instalasi & Konfigurasi Jaringan','desc'=>'Tim teknisi berpengalaman siap bantu setup infrastruktur LAN, WAN, maupun campus area network di lokasi Anda.'],
      ['icon'=>'yellow', 'title'=>'Konsultasi Infrastruktur IT',    'desc'=>'Belum tahu mulai dari mana? Kami bantu desain topologi jaringan sesuai kebutuhan skala bisnis Anda.'],
      ['icon'=>'',       'title'=>'Garansi & Dukungan Purna Jual',  'desc'=>'Semua produk bergaransi resmi. Support teknis tersedia via WhatsApp untuk respons cepat tanpa biaya tambahan.'],
    ];
  @endphp
  <div class="layanan-grid">
    @foreach ($layanans as $l)
      <article class="layanan-card">
        <div class="layanan-icon {{ $l['icon'] }}">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <div class="layanan-body">
          <h3>{{ $l['title'] }}</h3>
          <p>{{ $l['desc'] }}</p>
        </div>
      </article>
    @endforeach
  </div>
</section>

<section class="tentang" id="tentang">
  <div class="tentang-wrap">
    <div>
      <span class="kbd">⌘ TENTANG.02</span>
      <h2>Distributor jaringan terpercaya untuk bisnis Indonesia.</h2>
      <p class="tentang-lead">
        CV. SiJaring Nusantara berdiri sejak 2024 di Jember, Jawa Timur. Kami fokus menyediakan
        perangkat jaringan enterprise berkualitas dengan harga kompetitif, langsung ke tangan
        ISP lokal, sekolah, kantor, dan kontraktor IT di seluruh Indonesia.
      </p>
      <a href="{{ route('katalog') }}" class="btn-primary">Lihat Katalog Lengkap &rarr;</a>
    </div>
    <aside class="tentang-stats">
      <div><strong>2024</strong><span>tahun berdiri</span></div>
      <div><strong>{{ \App\Models\Produk::aktif()->distinct('kategori')->count('kategori') }}+</strong><span>kategori produk</span></div>
      <div><strong>150+</strong><span>klien aktif</span></div>
      <div><strong>24/7</strong><span>support teknis</span></div>
    </aside>
  </div>
</section>

{{-- ═══ FEATURE 1: Weather Widget (wttr.in, async/await) ═══ --}}
<section class="weather-section" id="weatherSection">
  <div class="weather-wrap">
    <div class="weather-header">
      <span class="kbd">⌘ CUACA.LIVE</span>
      <p class="weather-sub">Kondisi cuaca Jember hari ini — relevan untuk jadwal instalasi lapangan.</p>
    </div>
    <div class="weather-card" id="weatherCard">
      {{-- Loading state --}}
      <div class="weather-loading" id="weatherLoading">
        <div class="w-spinner"></div>
        <span>Mengambil data cuaca...</span>
      </div>
      {{-- Data state --}}
      <div class="weather-data" id="weatherData" hidden>
        <div class="weather-main">
          <div class="w-icon" id="wIcon">⛅</div>
          <div>
            <div class="w-city" id="wCity">Jember, Jawa Timur</div>
            <div class="w-temp" id="wTemp">--°C</div>
            <div class="w-desc" id="wDesc">—</div>
          </div>
        </div>
        <div class="weather-details">
          <div class="w-detail"><span class="w-label">Kelembapan</span><span class="w-val" id="wHumidity">--%</span></div>
          <div class="w-detail"><span class="w-label">Angin</span><span class="w-val" id="wWind">-- km/h</span></div>
          <div class="w-detail"><span class="w-label">Terasa</span><span class="w-val" id="wFeels">--°C</span></div>
          <div class="w-detail"><span class="w-label">Visibilitas</span><span class="w-val" id="wVisibility">-- km</span></div>
        </div>
        <div class="w-note"><span id="wInstallNote"></span></div>
      </div>
      {{-- Error state --}}
      <div class="weather-error" id="weatherError" hidden>
        <span>⚠ Gagal memuat data cuaca.</span>
        <button id="weatherRetry" class="btn-ghost" style="margin-left:1rem">Coba Lagi</button>
      </div>
    </div>
    <p class="weather-credit">
      // Sumber data: <a href="https://wttr.in" target="_blank" rel="noopener">wttr.in</a> — diperbarui setiap kunjungan
    </p>
  </div>
</section>

@endsection

@push('scripts')
<script>
/* ═══ FEATURE 1: Weather Widget — wttr.in API (async/await) ═══ */
const WEATHER_ICONS = {
  113: '☀️', 116: '⛅', 119: '☁️', 122: '🌫️', 143: '🌫️', 176: '🌦️',
  179: '🌨️', 182: '🌧️', 185: '🌧️', 200: '⛈️', 227: '🌨️', 230: '❄️',
  248: '🌫️', 260: '🌫️', 263: '🌦️', 266: '🌧️', 281: '🌧️', 284: '🌧️',
  293: '🌦️', 296: '🌧️', 299: '🌧️', 302: '🌧️', 305: '🌧️', 308: '🌧️',
  311: '🌧️', 314: '🌧️', 317: '🌨️', 320: '🌨️', 323: '🌨️', 326: '🌨️',
  329: '❄️', 332: '❄️', 335: '❄️', 338: '❄️', 350: '🌧️', 353: '🌦️',
  356: '🌧️', 359: '🌧️', 362: '🌨️', 365: '🌨️', 368: '🌨️', 371: '❄️',
  374: '🌨️', 377: '🌨️', 386: '⛈️', 389: '⛈️', 392: '⛈️', 395: '❄️',
};

async function fetchWeather() {
  const loading = document.getElementById('weatherLoading');
  const dataEl  = document.getElementById('weatherData');
  const errorEl = document.getElementById('weatherError');

  loading.hidden = false;
  dataEl.hidden  = true;
  errorEl.hidden = true;

  try {
    const response = await fetch('https://wttr.in/Jember?format=j1', {
      signal: AbortSignal.timeout(8000),
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data    = await response.json();
    const current = data.current_condition[0];
    const area    = data.nearest_area[0];

    const tempC       = current.temp_C;
    const feelsC      = current.FeelsLikeC;
    const humidity    = current.humidity;
    const windKmph    = current.windspeedKmph;
    const visibilityK = current.visibility;
    const weatherCode = parseInt(current.weatherCode);
    const descEN      = current.weatherDesc[0].value;
    const areaName    = area.areaName[0].value;
    const country     = area.country[0].value;

    const descMap = {
      'Sunny': 'Cerah', 'Clear': 'Cerah', 'Partly cloudy': 'Berawan sebagian',
      'Cloudy': 'Berawan', 'Overcast': 'Mendung', 'Mist': 'Berkabut',
      'Fog': 'Berkabut', 'Light rain': 'Hujan ringan', 'Moderate rain': 'Hujan sedang',
      'Heavy rain': 'Hujan deras', 'Light drizzle': 'Gerimis',
      'Patchy rain possible': 'Kemungkinan hujan', 'Patchy rain nearby': 'Kemungkinan hujan',
      'Thundery outbreaks possible': 'Kemungkinan badai petir',
      'Blizzard': 'Badai salju', 'Light snow': 'Salju ringan',
    };
    const descID = descMap[descEN] || descEN;

    let installNote = '';
    if (parseInt(tempC) > 33) {
      installNote = '⚠ Suhu tinggi — rekomendasikan instalasi pagi hari (sebelum pukul 10:00)';
    } else if (['🌧️','⛈️','🌦️'].includes(WEATHER_ICONS[weatherCode])) {
      installNote = '⚠ Cuaca kurang kondusif untuk instalasi outdoor — pertimbangkan reschedule';
    } else {
      installNote = '✓ Cuaca kondusif untuk instalasi jaringan outdoor hari ini';
    }

    document.getElementById('wCity').textContent       = `${areaName}, ${country}`;
    document.getElementById('wTemp').textContent       = `${tempC}°C`;
    document.getElementById('wDesc').textContent       = descID;
    document.getElementById('wIcon').textContent       = WEATHER_ICONS[weatherCode] || '🌤️';
    document.getElementById('wHumidity').textContent   = `${humidity}%`;
    document.getElementById('wWind').textContent       = `${windKmph} km/h`;
    document.getElementById('wFeels').textContent      = `${feelsC}°C`;
    document.getElementById('wVisibility').textContent = `${visibilityK} km`;
    document.getElementById('wInstallNote').textContent = installNote;

    loading.hidden = true;
    dataEl.hidden  = false;
  } catch (err) {
    console.error('Weather fetch error:', err);
    loading.hidden = true;
    errorEl.hidden = false;
  }
}

document.getElementById('weatherRetry')?.addEventListener('click', fetchWeather);
fetchWeather();
</script>
@endpush
