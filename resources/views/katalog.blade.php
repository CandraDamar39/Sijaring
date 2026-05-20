@extends('layouts.app')

@section('title', 'Katalog — CV. SiJaring Nusantara')

@push('page-styles')
<style>
  .catalog { max-width: var(--container); margin: 0 auto; padding: 0 1.1rem 5rem; }
  .admin-bar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding: 1rem 0; margin-bottom: 1rem; border-bottom: 1.5px dashed var(--line); }
  .admin-bar span { font-family: var(--font-mono); font-size: .78rem; color: var(--muted); }
  .product-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
  .product { background: var(--bg); border: 1.5px solid var(--ink); border-radius: var(--radius); overflow: hidden; display: flex; flex-direction: column; transition: transform .3s var(--ease), box-shadow .3s var(--ease); position: relative; }
  .product:hover { transform: translate(-3px,-6px); box-shadow: 8px 12px 0 0 var(--ink); }
  .product-media { position: relative; aspect-ratio: 16/10; display: grid; place-items: center; border-bottom: 1.5px solid var(--ink); overflow: hidden; }
  .product-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s var(--ease); }
  .product:hover .product-media img { transform: scale(1.05); }
  .tone-pink { background: var(--accent-pink); } .tone-cyan { background: var(--accent-cyan); }
  .tone-yellow { background: var(--accent-yellow); } .tone-cream { background: var(--bg-2); }
  .tone-green { background: var(--accent-green); } .tone-ink { background: var(--ink); }
  .product-body { padding: 1.1rem; display: flex; flex-direction: column; gap: .35rem; flex: 1; }
  .product-body h3 { font-family: var(--font-body); font-size: 1.05rem; font-weight: 600; margin: 0; letter-spacing: -.01em; }
  .product-body .spec { font-family: var(--font-mono); font-size: .75rem; color: var(--muted); margin: 0 0 .8rem; }
  .price-row { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
  .price { font-family: var(--font-display); font-size: 1.05rem; }
  .btn-buy { background: var(--ink); color: var(--bg); padding: .55rem 1rem; border-radius: 999px; font-size: .85rem; font-weight: 600; }
  .btn-buy:hover { background: var(--accent-pink); transform: translateY(-2px); box-shadow: 0 4px 0 var(--ink); }
  .product-admin { position: absolute; top: .6rem; right: .6rem; display: flex; gap: .4rem; z-index: 5; opacity: 0; transition: opacity .2s; }
  .product:hover .product-admin { opacity: 1; }
  .admin-btn { width: 30px; height: 30px; border-radius: 6px; display: grid; place-items: center; font-size: .8rem; font-weight: 700; border: 1.5px solid var(--ink); cursor: pointer; }
  .admin-btn.edit { background: var(--accent-yellow); color: var(--ink); }
  .admin-btn.del { background: #fff; color: var(--accent-pink); border-color: var(--accent-pink); }
  @media (min-width: 640px) { .product-grid { grid-template-columns: repeat(2, 1fr); gap: 1.2rem; } }
  @media (min-width: 1024px) { .product-grid { grid-template-columns: repeat(3, 1fr); gap: 1.4rem; } }

  /* ── Live Search Toolbar (Feature 2) ── */
  .catalog-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; padding: 1rem 0; margin-bottom: .4rem; border-bottom: 1.5px dashed var(--line); }
  .toolbar-count { font-family: var(--font-mono); font-size: .78rem; color: var(--muted); }
  .search-group { display: flex; gap: .6rem; flex-wrap: wrap; align-items: center; }
  .search-input-wrap { position: relative; display: inline-flex; align-items: center; }
  .search-icon { position: absolute; left: .9rem; width: 18px; height: 18px; color: var(--muted); pointer-events: none; }
  .search-spinner { position: absolute; right: .9rem; width: 16px; height: 16px; border: 2px solid var(--line); border-top-color: var(--accent-cyan); border-radius: 50%; animation: spin .8s linear infinite; }
  .empty-grid { grid-column: 1/-1; text-align: center; color: var(--muted); font-family: var(--font-mono); padding: 3rem 0; }

  /* ── Visit Counter (Feature 4) ── */
  .visit-counter { margin: 1.2rem 0 1.5rem; padding: .9rem 1.2rem; background: var(--bg-2); border: 1.5px solid var(--ink); border-radius: 12px; font-family: var(--font-mono); }
  .vc-inner { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .8rem; }
  .vc-stats { display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap; }
  .vc-stat { display: flex; flex-direction: column; gap: .15rem; }
  .vc-num { font-family: var(--font-display); font-size: 1.5rem; font-weight: 900; color: var(--accent-pink); line-height: 1; }
  .vc-label { font-size: .65rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
  .vc-time { font-size: .82rem; font-weight: 600; color: var(--ink); }
  .vc-divider { width: 1px; height: 32px; background: var(--line); flex-shrink: 0; }
  .vc-reset { font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; padding: .4rem .9rem; border-radius: 6px; border: 1.5px solid var(--ink); background: transparent; color: var(--ink); cursor: pointer; transition: all .2s; }
  .vc-reset:hover { background: var(--accent-pink); color: #fff; border-color: var(--accent-pink); }
</style>
@endpush

@section('content')

<div class="page-hero">
  <span class="kbd">⌘ KATALOG.02</span>
  <h1>Alat jaringan,<br>siap kirim hari ini.</h1>
  <p>Switch, router, kabel, access point — stok tersedia, harga transparan.</p>
</div>

<section class="catalog">

  {{-- FEATURE 2: Live Search Toolbar --}}
  <div class="catalog-toolbar">
    <span class="toolbar-count" id="toolbarCount">// {{ $totalProduk }} produk tersedia</span>
    <div class="search-group">
      <div class="search-input-wrap">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="liveSearch" placeholder="Cari produk... (mis. switch, router, kabel)"
               autocomplete="off" value="{{ $search }}"
               style="padding:.7rem 2.6rem .7rem 2.6rem;border:1.5px solid var(--ink);border-radius:999px;font-family:var(--font-body);font-size:.95rem;width:min(320px,100%);background:var(--bg);color:var(--ink);outline:none;transition:box-shadow .2s">
        <div class="search-spinner" id="searchSpinner" hidden></div>
      </div>
      <select id="filterKategori"
              style="padding:.7rem 1rem;border:1.5px solid var(--ink);border-radius:999px;font-family:var(--font-body);font-size:.9rem;background:var(--bg);color:var(--ink);cursor:pointer">
        <option value="all">Semua Kategori</option>
        @foreach ($kategoris as $k)
          <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
        @endforeach
      </select>
    </div>
    @auth
      @if (Auth::user()->isAdmin())
        <a href="{{ route('admin.produk') }}" class="btn-primary">+ Kelola Produk</a>
      @endif
    @endauth
  </div>

  {{-- FEATURE 4: Session Visit Counter --}}
  <div class="visit-counter" id="visitCounter">
    <div class="vc-inner">
      <div class="vc-stats">
        <div class="vc-stat">
          <span class="vc-num">{{ $visits }}</span>
          <span class="vc-label">Kunjungan</span>
        </div>
        <div class="vc-divider"></div>
        <div class="vc-stat">
          <span class="vc-label">Pertama</span>
          <span class="vc-time">{{ \Carbon\Carbon::parse($first)->locale('id')->isoFormat('D MMM HH:mm') }}</span>
        </div>
        <div class="vc-divider"></div>
        <div class="vc-stat">
          <span class="vc-label">Terakhir</span>
          <span class="vc-time">{{ \Carbon\Carbon::parse($last)->locale('id')->isoFormat('D MMM HH:mm') }}</span>
        </div>
      </div>
      <form method="POST" action="{{ route('katalog.reset-visits') }}" style="display:inline">
        @csrf
        <button type="submit" class="vc-reset" onclick="return confirm('Reset hitungan kunjungan?')">Reset Hitungan</button>
      </form>
    </div>
  </div>

  {{-- Result info (AJAX) --}}
  <div id="searchResultInfo" style="display:none;font-family:var(--font-mono);font-size:.78rem;color:var(--muted);margin-bottom:1rem">
    // Menampilkan <strong id="resultCount">0</strong> produk untuk pencarian "<em id="resultQuery"></em>"
  </div>

  <div class="product-grid" id="productGrid">
    @forelse ($produk as $p)
      @include('partials.product-card', ['p' => $p])
    @empty
      <p class="empty-grid">// Tidak ada produk yang cocok.</p>
    @endforelse
  </div>
</section>
@endsection

@section('modals')
{{-- ===== CART DRAWER ===== --}}
<div class="cart-backdrop" id="cartBackdrop" hidden></div>
<aside class="cart-drawer" id="cartDrawer" aria-hidden="true">
  <header class="cart-head">
    <div>
      <span class="kbd">⌘ KERANJANG</span>
      <h3>Yang mau dibawa pulang?</h3>
    </div>
    <button class="icon-btn" id="cartClose" aria-label="Tutup">✕</button>
  </header>
  <ul class="cart-list" id="cartList"></ul>
  <div class="cart-empty" id="cartEmpty">
    <span class="mono">// keranjang masih kosong</span>
    <p>Tambahkan perangkat dari katalog di atas.</p>
  </div>
  <footer class="cart-foot" id="cartFoot" hidden>
    <div class="cart-row"><span>Subtotal</span><strong id="cartSubtotal">Rp 0</strong></div>
    <div class="cart-row small"><span>Ongkir</span><span>dihitung saat checkout</span></div>
    <button class="btn-primary big full" id="goCheckout" style="margin-top:.8rem">Lanjutkan Pesanan &rarr;</button>
  </footer>
</aside>

{{-- ===== CHECKOUT MODAL ===== --}}
<div class="modal" id="checkoutModal" role="dialog" aria-hidden="true">
  <div class="modal-card">
    <header class="modal-head">
      <div><span class="kbd">⌘ CHECKOUT.03</span><h3>Selesaikan pesanan.</h3></div>
      <button class="icon-btn" data-close-modal>✕</button>
    </header>
    <form class="checkout-form" id="checkoutForm" novalidate>
      <div class="checkout-grid">
        <section class="checkout-section">
          <h4>Pengiriman</h4>
          <div class="field"><label>Nama / PIC</label><input name="name" required placeholder="Budi Santoso"></div>
          <div class="field"><label>Perusahaan <span class="opt">opsional</span></label><input name="company" placeholder="PT / CV / Sekolah"></div>
          <div class="field-row">
            <div class="field"><label>Email</label><input name="email" type="email" required placeholder="nama@email.com"></div>
            <div class="field"><label>WhatsApp</label><input name="phone" type="tel" required placeholder="+62 ..."></div>
          </div>
          <div class="field"><label>Alamat</label><textarea name="address" required rows="3" placeholder="Jalan, no., RT/RW..."></textarea></div>
          <div class="field-row">
            <div class="field"><label>Kota</label><input name="city" required placeholder="Jember"></div>
            <div class="field"><label>Kode Pos</label><input name="zip" required inputmode="numeric" placeholder="68122"></div>
          </div>
          <h4 class="mt">Metode Bayar</h4>
          <div class="pay-options">
            <label class="pay-opt"><input type="radio" name="pay" value="bca" checked><span class="pay-bg">BCA</span><span class="pay-lbl">BCA Transfer</span></label>
            <label class="pay-opt"><input type="radio" name="pay" value="mandiri"><span class="pay-bg yellow">MDR</span><span class="pay-lbl">Mandiri</span></label>
            <label class="pay-opt"><input type="radio" name="pay" value="bni"><span class="pay-bg orange">BNI</span><span class="pay-lbl">BNI Transfer</span></label>
            <label class="pay-opt"><input type="radio" name="pay" value="qris"><span class="pay-bg pink">QRIS</span><span class="pay-lbl">QRIS</span></label>
            <label class="pay-opt"><input type="radio" name="pay" value="cod"><span class="pay-bg cyan">COD</span><span class="pay-lbl">Bayar di Tempat</span></label>
          </div>
        </section>
        <section class="checkout-section">
          <h4>Ringkasan Pesanan</h4>
          <ul class="sum-list" id="sumList"></ul>
          <div class="cart-row"><span>Subtotal</span><span id="sumSubtotal">Rp 0</span></div>
          <div class="cart-row"><span>Ongkir (Jember &amp; Jatim)</span><span id="sumShip">Rp 25.000</span></div>
          <div class="cart-row total"><strong>Total Tagihan</strong><strong id="sumTotal">Rp 0</strong></div>
        </section>
      </div>
      <footer class="checkout-foot">
        <button type="button" class="btn-ghost big" data-close-modal>Kembali</button>
        <button type="submit" class="btn-primary big">Konfirmasi Pesanan &rarr;</button>
      </footer>
    </form>
  </div>
</div>

{{-- ===== CONFIRMATION MODAL ===== --}}
<div class="modal" id="confirmModal" role="dialog" aria-hidden="true">
  <div class="modal-card confirm-card">
    <div class="confirm-stamp">TERKIRIM</div>
    <span class="kbd">⌘ ORDER.04</span>
    <h3>Pesanan kamu tercatat.</h3>
    <p class="confirm-lead">Tim sales bakal kontak via WhatsApp untuk konfirmasi metode bayar &amp; estimasi pengiriman.</p>
    <div class="confirm-block">
      <div class="confirm-row"><span class="mono">Order ID</span><strong id="orderId">—</strong></div>
      <div class="confirm-row"><span class="mono">Status</span><strong style="color:var(--accent-pink)">Menunggu Konfirmasi</strong></div>
      <div class="confirm-row total"><span>Total</span><strong id="orderTotal">—</strong></div>
    </div>
    <div class="confirm-foot">
      <a class="btn-ghost big" id="waLink" href="#" target="_blank" rel="noopener">Chat Sales WhatsApp</a>
      <button class="btn-primary big" id="confirmDone">Tutup</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  window.SJ = window.SJ || {};
  window.SJ.checkoutUrl = @json(route('checkout'));
  window.SJ.csrf        = document.querySelector('meta[name="csrf-token"]').content;
</script>

<script>
/* ═══ FEATURE 2: Live Search AJAX (debounce, no reload) ═══ */
(function () {
  const searchInput  = document.getElementById('liveSearch');
  const filterSelect = document.getElementById('filterKategori');
  const productGrid  = document.getElementById('productGrid');
  const spinner      = document.getElementById('searchSpinner');
  const countBadge   = document.getElementById('toolbarCount');
  const resultInfo   = document.getElementById('searchResultInfo');
  const resultCount  = document.getElementById('resultCount');
  const resultQuery  = document.getElementById('resultQuery');
  const SEARCH_URL   = @json(route('api.search-produk'));

  let debounceTimer = null;

  function esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => (
      { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
    ));
  }

  function buildCard(p) {
    const foto = p.foto ? `<img src="${esc(p.foto)}" alt="${esc(p.nama)}" loading="lazy"/>` : '';
    return `
      <article class="product" data-id="${esc(p.kode)}" data-kat="${esc(p.kategori)}">
        <div class="product-media ${esc(p.tone)}">${foto}</div>
        <div class="product-body">
          <h3>${esc(p.nama)}</h3>
          <p class="spec">${esc(p.spec)} · stok: ${esc(p.stok)}</p>
          <div class="price-row">
            <span class="price">${esc(p.harga_fmt)}</span>
            <button class="btn-buy" data-add
                    data-id="${esc(p.kode)}"
                    data-name="${esc(p.nama)}"
                    data-spec="${esc(p.spec)}"
                    data-price="${p.harga}">+ Keranjang</button>
          </div>
        </div>
      </article>`;
  }

  async function doSearch() {
    const q        = searchInput.value.trim();
    const kategori = filterSelect.value;

    spinner.hidden = false;
    searchInput.style.boxShadow = '0 0 0 3px rgba(0,212,224,.2)';

    try {
      const url = new URL(SEARCH_URL, location.origin);
      url.searchParams.set('q', q);
      url.searchParams.set('kategori', kategori);

      const res = await fetch(url.toString(), {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      if (json.produk.length === 0) {
        productGrid.innerHTML =
          `<p class="empty-grid">// Tidak ada produk yang cocok dengan "<strong>${esc(q)}</strong>"</p>`;
      } else {
        productGrid.innerHTML = json.produk.map(buildCard).join('');
      }

      countBadge.textContent = `// ${json.count} produk tersedia`;

      if (q || kategori !== 'all') {
        resultInfo.style.display = 'block';
        resultCount.textContent  = json.count;
        resultQuery.textContent  = q || `kategori: ${kategori}`;
      } else {
        resultInfo.style.display = 'none';
      }
    } catch (err) {
      console.error('Search error:', err);
      productGrid.innerHTML =
        `<p class="empty-grid" style="color:var(--accent-pink)">⚠ Gagal memuat produk. Coba lagi.</p>`;
    } finally {
      spinner.hidden = true;
      searchInput.style.boxShadow = '';
    }
  }

  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(doSearch, 350);
  });
  filterSelect.addEventListener('change', doSearch);
})();
</script>
@endpush
