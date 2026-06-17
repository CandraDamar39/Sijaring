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
      transform: translateY(180px);   /* turunkan router agar sejajar dengan judul */
    }
    .hero-title { font-size: clamp(3rem, 6.5vw, 5rem); }
    .layanan-grid { grid-template-columns: repeat(2, 1fr); }
    .tentang-wrap { grid-template-columns: 1fr auto; align-items: center; }
    .tentang-stats { grid-template-columns: repeat(4, 1fr); }
  }

  /* ── Speed Test Section (Feature 1 — AJAX async ke server Laravel) ── */
  .speedtest-section { max-width: var(--container); margin: 0 auto; padding: 3rem 1.4rem; }
  .speedtest-wrap { max-width: 760px; }
  .speedtest-header { margin-bottom: 1.2rem; }
  .speedtest-sub { color: var(--muted); font-size: .95rem; margin: .5rem 0 0; }
  .speedtest-card { background: var(--bg); border: 2px solid var(--ink); border-radius: 16px; padding: 1.8rem 1.6rem; box-shadow: 6px 8px 0 0 var(--ink); }
  .st-gauges { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.4rem; }
  @media (max-width: 600px) { .st-gauges { grid-template-columns: 1fr; } }
  .st-gauge { border: 1px dashed var(--line); border-radius: 12px; padding: 1.1rem 1rem; text-align: center; transition: border-color .2s, box-shadow .2s; }
  .st-gauge.active { border-style: solid; border-color: var(--accent-cyan); box-shadow: 3px 3px 0 0 var(--accent-cyan); }
  .st-gauge-label { font-family: var(--font-mono); font-size: .68rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin-bottom: .5rem; }
  .st-gauge-value { font-family: var(--font-display); font-size: 2rem; font-weight: 900; line-height: 1; letter-spacing: -.02em; }
  .st-gauge-unit { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); margin-top: .35rem; }
  .st-gauge.ping .st-gauge-value { color: var(--accent-pink); }
  .st-gauge.down .st-gauge-value { color: var(--accent-cyan); }
  .st-gauge.up   .st-gauge-value { color: var(--accent-green); }
  .st-progress { height: 8px; background: var(--gray-100); border-radius: 99px; overflow: hidden; margin-bottom: 1rem; }
  .st-progress-bar { height: 100%; width: 0; background: var(--accent-cyan); transition: width .25s ease; }
  .st-status { display: flex; align-items: center; gap: .8rem; min-height: 24px; font-family: var(--font-mono); font-size: .82rem; color: var(--muted); margin-bottom: 1rem; }
  .st-spinner { width: 20px; height: 20px; border: 3px solid var(--line); border-top-color: var(--accent-cyan); border-radius: 50%; animation: spin 0.8s linear infinite; flex-shrink: 0; }
  .st-note { font-family: var(--font-mono); font-size: .78rem; color: var(--accent-green); padding: .6rem .8rem; background: rgba(46,204,113,.08); border-radius: 8px; border-left: 3px solid var(--accent-green); margin-bottom: 1rem; }
  .st-note:empty { display: none; }
  .st-error { font-family: var(--font-mono); font-size: .82rem; color: var(--accent-pink); margin-bottom: 1rem; }
  .speedtest-credit { font-family: var(--font-mono); font-size: .7rem; color: var(--muted); margin-top: .8rem; }
  .speedtest-credit code { background: var(--gray-100); padding: .1rem .35rem; border-radius: 4px; }
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

{{-- ═══ FEATURE 1: Speed Test Jaringan (AJAX async ke server, tanpa reload) ═══ --}}
<section class="speedtest-section" id="speedtestSection">
  <div class="speedtest-wrap">
    <div class="speedtest-header">
      <span class="kbd">⌘ NETWORK.SPEEDTEST</span>
      <p class="speedtest-sub">Uji kualitas koneksi internet Anda — ping, download, dan upload. Tes berkomunikasi langsung dengan server kami via Fetch API, tanpa reload halaman.</p>
    </div>
    <div class="speedtest-card">
      {{-- Hasil pengukuran --}}
      <div class="st-gauges">
        <div class="st-gauge ping" id="gaugePing">
          <div class="st-gauge-label">Ping</div>
          <div class="st-gauge-value" id="valPing">—</div>
          <div class="st-gauge-unit">ms</div>
        </div>
        <div class="st-gauge down" id="gaugeDown">
          <div class="st-gauge-label">Download</div>
          <div class="st-gauge-value" id="valDown">—</div>
          <div class="st-gauge-unit">Mbps</div>
        </div>
        <div class="st-gauge up" id="gaugeUp">
          <div class="st-gauge-label">Upload</div>
          <div class="st-gauge-value" id="valUp">—</div>
          <div class="st-gauge-unit">Mbps</div>
        </div>
      </div>

      {{-- Progress + status (loading indicator) --}}
      <div class="st-progress"><div class="st-progress-bar" id="stProgressBar"></div></div>
      <div class="st-status" id="stStatus"><span>Siap menguji koneksi Anda.</span></div>

      {{-- Rekomendasi adaptif + error state --}}
      <div class="st-note"><span id="stNote"></span></div>
      <div class="st-error" id="stError" hidden></div>

      <button class="btn-primary" id="stStart">▶ Mulai Tes Kecepatan</button>
    </div>
    <p class="speedtest-credit">
      // Endpoint: <code>/api/speedtest/*</code> — diukur real-time terhadap server aplikasi
    </p>
  </div>
</section>

@endsection

@push('scripts')
<script>
/* ═══ FEATURE 1: Speed Test Jaringan — Fetch API async/await ke server Laravel ═══ */
(function () {
  const startBtn = document.getElementById('stStart');
  if (!startBtn) return;

  const elStatus = document.getElementById('stStatus');
  const elBar    = document.getElementById('stProgressBar');
  const elError  = document.getElementById('stError');
  const elNote   = document.getElementById('stNote');
  const valPing  = document.getElementById('valPing');
  const valDown  = document.getElementById('valDown');
  const valUp    = document.getElementById('valUp');
  const gPing    = document.getElementById('gaugePing');
  const gDown    = document.getElementById('gaugeDown');
  const gUp      = document.getElementById('gaugeUp');
  const csrf     = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const PING_URL = @json(route('speedtest.ping'));
  const DOWN_URL = @json(route('speedtest.download'));
  const UP_URL   = @json(route('speedtest.upload'));

  const setStatus = (html, spinner = false) => {
    elStatus.innerHTML = (spinner ? '<div class="st-spinner"></div>' : '') + '<span>' + html + '</span>';
  };
  const setBar = (pct) => { elBar.style.width = Math.max(0, Math.min(100, pct)) + '%'; };
  const clearActive = () => [gPing, gDown, gUp].forEach(g => g.classList.remove('active'));

  // ── 1. PING: rata-rata beberapa round-trip (buang sampel terburuk) ──
  async function measurePing(samples = 5) {
    gPing.classList.add('active');
    const times = [];
    for (let i = 0; i < samples; i++) {
      const t0 = performance.now();
      await fetch(PING_URL + '?_=' + Date.now() + '-' + i, { cache: 'no-store' });
      times.push(performance.now() - t0);
      setBar(5 + ((i + 1) / samples) * 20);
    }
    times.sort((a, b) => a - b);
    const best = times.slice(0, Math.max(1, times.length - 1));
    const avg  = best.reduce((a, b) => a + b, 0) / best.length;
    valPing.textContent = avg.toFixed(0);
    gPing.classList.remove('active');
    return avg;
  }

  // ── 2. DOWNLOAD: unduh N byte sambil menghitung throughput secara live ──
  async function measureDownload(bytes = 10000000) {
    gDown.classList.add('active');
    const t0  = performance.now();
    const res = await fetch(DOWN_URL + '?bytes=' + bytes + '&_=' + Date.now(), { cache: 'no-store' });
    if (!res.ok || !res.body) throw new Error('HTTP ' + res.status);
    const reader = res.body.getReader();
    let received = 0;
    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      received += value.length;
      setBar(25 + (received / bytes) * 45);
      const secs = (performance.now() - t0) / 1000;
      if (secs > 0) valDown.textContent = ((received * 8) / (secs * 1e6)).toFixed(1);
    }
    const secs = (performance.now() - t0) / 1000;
    const mbps = (received * 8) / (secs * 1e6);
    valDown.textContent = mbps.toFixed(1);
    gDown.classList.remove('active');
    return mbps;
  }

  // ── 3. UPLOAD: kirim N byte ke server, ukur throughput ──
  async function measureUpload(bytes = 4000000) {
    gUp.classList.add('active');
    const payload = new Uint8Array(bytes);
    const t0  = performance.now();
    const res = await fetch(UP_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/octet-stream' },
      body: payload,
      cache: 'no-store',
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const secs = (performance.now() - t0) / 1000;
    const mbps = (bytes * 8) / (secs * 1e6);
    valUp.textContent = mbps.toFixed(1);
    setBar(100);
    gUp.classList.remove('active');
    return mbps;
  }

  // Rekomendasi adaptif sesuai hasil — dikaitkan dengan bisnis Si Jaring.
  function recommend(down) {
    if (down >= 100) return '✓ Koneksi sangat baik — siap untuk VoIP, CCTV multi-channel, dan kantor padat pengguna.';
    if (down >= 20)  return '✓ Koneksi memadai untuk kantor kecil–menengah dan akses cloud harian.';
    if (down >= 5)   return '• Koneksi cukup untuk browsing & email; pertimbangkan upgrade bila banyak perangkat.';
    return '⚠ Koneksi tergolong lambat — konsultasikan upgrade infrastruktur jaringan dengan tim kami.';
  }

  async function runTest() {
    startBtn.disabled = true;
    startBtn.textContent = '⏳ Menguji...';
    elError.hidden = true;
    elNote.textContent = '';
    valPing.textContent = valDown.textContent = valUp.textContent = '—';
    clearActive();
    setBar(0);

    try {
      setStatus('Mengukur latensi (ping)...', true);
      await measurePing();

      setStatus('Mengukur kecepatan download...', true);
      const down = await measureDownload();

      setStatus('Mengukur kecepatan upload...', true);
      await measureUpload();

      clearActive();
      setBar(100);
      setStatus('✓ Tes selesai.');
      elNote.textContent = recommend(down);
    } catch (err) {
      console.error('Speedtest error:', err);
      clearActive();
      setStatus('');
      elError.hidden = false;
      elError.textContent = '⚠ Tes gagal: ' + (err.message || 'koneksi terputus') + '. Silakan coba lagi.';
    } finally {
      startBtn.disabled = false;
      startBtn.textContent = '↻ Tes Ulang';
    }
  }

  startBtn.addEventListener('click', runTest);
})();
</script>
@endpush
