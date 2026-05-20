@extends('layouts.app')

@section('title', 'Keranjang Rekomendasi — Si Jaring Nusantara')

@push('page-styles')
<style>
  .keranjang-page { max-width: 900px; margin: 0 auto; padding: 7rem 1.4rem 4rem; min-height: 55vh; }
  .rec-table {
    background: var(--bg); border: 2px solid var(--ink);
    border-radius: 14px; box-shadow: 6px 8px 0 0 var(--ink);
    overflow: hidden; margin: 1.5rem 0 1rem;
  }
  .rec-row { display: grid; grid-template-columns: 70px 1fr auto; gap: 1rem; padding: 1rem 1.2rem; border-bottom: 1px dashed rgba(15,14,12,.12); align-items: center; }
  .rec-row:last-child { border-bottom: none; }
  .rec-thumb { width: 70px; height: 70px; border-radius: 8px; border: 1.5px solid var(--ink); background: var(--bg-2); overflow: hidden; }
  .rec-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .rec-info h4 { font-family: var(--font-body); font-size: 1rem; font-weight: 600; margin: 0 0 .2rem; }
  .rec-info .spec { font-family: var(--font-mono); font-size: .75rem; color: var(--muted); }
  .rec-info .kat {
    display: inline-block; font-family: var(--font-mono); font-size: .65rem;
    padding: .15rem .4rem; border-radius: 4px;
    background: var(--accent-pink); color: #fff;
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: .3rem;
  }
  .rec-price { font-family: var(--font-display); font-size: 1.05rem; font-weight: 700; text-align: right; }

  .keranjang-summary {
    background: var(--ink); color: var(--bg);
    border-radius: 14px; padding: 1.8rem; margin-top: 1.5rem;
    display: grid; grid-template-columns: 1fr auto; gap: 1.5rem;
    align-items: center; flex-wrap: wrap;
    border: 2px solid var(--ink); box-shadow: 6px 8px 0 0 var(--accent-pink);
  }
  @media (max-width: 700px) { .keranjang-summary { grid-template-columns: 1fr; } }
  .summary-label { font-family: var(--font-mono); font-size: .8rem; color: #c8c2b3; text-transform: uppercase; letter-spacing: .08em; display: block; margin-bottom: .35rem; }
  .summary-harga { font-family: var(--font-display); font-size: 2rem; font-weight: 900; color: var(--accent-pink); display: block; line-height: 1; }
  .summary-note { font-family: var(--font-mono); font-size: .72rem; color: #b8b3a4; margin: .6rem 0 0; line-height: 1.5; }
  .summary-actions { display: flex; gap: .6rem; flex-wrap: wrap; }
  .btn-keranjang {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .85rem 1.4rem; border-radius: 10px;
    font-family: var(--font-display); font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; font-size: .88rem;
    border: 2px solid var(--accent-pink); cursor: pointer;
    transition: transform .2s, box-shadow .2s;
  }
  .btn-keranjang.solid { background: var(--accent-pink); color: #fff; }
  .btn-keranjang.solid:hover { transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 var(--bg); }
  .btn-keranjang.outline { background: transparent; color: var(--bg); border-color: var(--bg); }
  .btn-keranjang.outline:hover { background: var(--bg); color: var(--ink); }

  /* Pay radio styling */
  .pay-label { display: inline-block; padding: .4rem .85rem; border: 1.5px solid var(--ink); border-radius: 8px; font-family: var(--font-mono); font-size: .8rem; cursor: pointer; transition: all .2s; background: var(--bg); }
  .pay-label.active { background: var(--ink); color: var(--bg); }
</style>
@endpush

@section('content')
<div class="keranjang-page">

  <div class="page-head">
    <div>
      <span class="kbd">⌘ KERANJANG.07</span>
      <h1>Keranjang<br><span class="text-pink">Rekomendasi</span></h1>
      <p class="lead">Berdasarkan input Network Builder kamu, ini paket yang kami rekomendasikan.</p>
    </div>
    <form method="POST" action="{{ route('keranjang.clear') }}" style="display:inline">
      @csrf
      <button type="submit" class="btn-ghost">Kosongkan</button>
    </form>
  </div>

  @if ($rekomendasi->isEmpty())
    <div class="empty-state">
      <div class="ic">🛒</div>
      <h3>Keranjang kosong</h3>
      <p>Belum ada rekomendasi. Coba isi form Network Builder dulu.</p>
      <a href="{{ route('builder') }}" class="btn">Buka Network Builder</a>
    </div>
  @else
    <div class="rec-table">
      @foreach ($rekomendasi as $p)
        <div class="rec-row">
          <div class="rec-thumb">
            @if ($p->foto)<img src="{{ $p->foto }}" alt="{{ $p->nama }}" loading="lazy">@endif
          </div>
          <div class="rec-info">
            <span class="kat">{{ $p->kategori }}</span>
            <h4>{{ $p->nama }}</h4>
            <span class="spec">{{ $p->spec }}</span>
          </div>
          <div class="rec-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
        </div>
      @endforeach
    </div>

    @php
      $waLink = \App\Models\ContactSetting::get('whatsapp_link', '6282329277901');
      $waText = urlencode(
        "Halo Si Jaring, saya tertarik dengan paket jaringan senilai Rp " . number_format($totalHarga, 0, ',', '.') . "!"
      );
    @endphp

    <div class="keranjang-summary">
      <div class="summary-total">
        <span class="summary-label">Total Estimasi</span>
        <span class="summary-harga">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
        <p class="summary-note">*Belum termasuk ongkir Rp 25.000. Konfirmasi final via WhatsApp.</p>
      </div>
      <div class="summary-actions">
        @auth
          <button type="button" class="btn-keranjang solid" id="checkoutBuilderBtn">Pesan Sekarang &rarr;</button>
        @else
          <a href="{{ route('login') }}" class="btn-keranjang solid">Masuk untuk Memesan &rarr;</a>
        @endauth
        <a href="https://wa.me/{{ $waLink }}?text={{ $waText }}" target="_blank" rel="noopener" class="btn-keranjang outline">Chat WhatsApp</a>
      </div>
    </div>

    <p style="margin-top:1.4rem;font-family:var(--font-mono);font-size:.78rem;color:var(--muted);line-height:1.6">
      // Harga di atas estimasi katalog. Tim sales akan kirim quotation final via WhatsApp dengan diskon volume + ongkir.
    </p>
  @endif

</div>
@endsection

@if (!$rekomendasi->isEmpty() && auth()->check())
@section('modals')
{{-- Checkout Modal --}}
<div class="modal" id="checkoutBuilderModal" role="dialog" aria-hidden="true">
  <div class="modal-card">
    <header class="modal-head">
      <div>
        <span class="kbd">⌘ CHECKOUT</span>
        <h3>Selesaikan pesanan.</h3>
      </div>
      <button type="button" class="icon-btn" id="closeCheckoutBuilder">✕</button>
    </header>

    <form id="builderCheckoutForm" style="padding:1.4rem;display:grid;gap:1rem">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="field">
          <label for="bName">Nama / PIC</label>
          <input id="bName" name="name" required placeholder="Budi Santoso" value="{{ Auth::user()?->name }}"/>
        </div>
        <div class="field">
          <label for="bCompany">Perusahaan (opsional)</label>
          <input id="bCompany" name="company" placeholder="PT / CV / Sekolah" value="{{ Auth::user()?->company }}"/>
        </div>
        <div class="field">
          <label for="bEmail">Email</label>
          <input id="bEmail" name="email" type="email" required value="{{ Auth::user()?->email }}"/>
        </div>
        <div class="field">
          <label for="bPhone">No. WhatsApp</label>
          <input id="bPhone" name="phone" type="tel" required value="{{ Auth::user()?->phone }}"/>
        </div>
        <div class="field" style="grid-column:1/-1">
          <label for="bAddress">Alamat Lengkap</label>
          <textarea id="bAddress" name="address" rows="2" required placeholder="Jalan, no., RT/RW, kelurahan…">{{ Auth::user()?->address }}</textarea>
        </div>
        <div class="field">
          <label for="bCity">Kota / Kab.</label>
          <input id="bCity" name="city" required value="{{ Auth::user()?->city ?? 'Jember' }}"/>
        </div>
        <div class="field">
          <label for="bZip">Kode Pos</label>
          <input id="bZip" name="zip" required inputmode="numeric" value="{{ Auth::user()?->zip ?? '68122' }}"/>
        </div>
      </div>

      {{-- Payment method --}}
      <div>
        <p style="font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.7rem">Metode Bayar</p>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap">
          @foreach (['bca'=>'BCA','mandiri'=>'Mandiri','bni'=>'BNI','qris'=>'QRIS','cod'=>'COD'] as $val => $label)
            <label style="cursor:pointer">
              <input type="radio" name="pay" value="{{ $val }}" {{ $val==='bca' ? 'checked' : '' }} class="pay-radio" style="display:none"/>
              <span class="pay-label {{ $val==='bca' ? 'active' : '' }}">{{ $label }}</span>
            </label>
          @endforeach
        </div>
      </div>

      {{-- Order summary --}}
      <div style="background:var(--bg-2);border-radius:10px;padding:1rem">
        <strong style="font-family:var(--font-display);font-size:.9rem;display:block;margin-bottom:.6rem">
          Ringkasan ({{ count($builderCart) }} item)
        </strong>
        @foreach ($builderCart as $item)
          <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.88rem;border-bottom:1px dashed rgba(15,14,12,.12)">
            <span>{{ $item['name'] }}</span>
            <span style="font-family:var(--font-mono);font-weight:600">Rp {{ number_format($item['price'],0,',','.') }}</span>
          </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;padding:.6rem 0 0;font-weight:700">
          <span>Total (+ ongkir Rp 25.000)</span>
          <span style="font-family:var(--font-mono);color:var(--accent-pink)">Rp {{ number_format($totalHarga + 25000, 0, ',', '.') }}</span>
        </div>
      </div>

      <div style="display:flex;gap:.8rem;justify-content:flex-end">
        <button type="button" id="closeCheckoutBuilder2" class="btn-ghost">Batal</button>
        <button type="submit" class="btn-primary">Konfirmasi Pesanan &rarr;</button>
      </div>
    </form>
  </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal" id="confirmBuilderModal" role="dialog" aria-hidden="true">
  <div class="modal-card confirm-card">
    <div class="confirm-stamp">TERKIRIM</div>
    <span class="kbd">⌘ ORDER.MASUK</span>
    <h3>Pesanan tercatat.</h3>
    <p class="confirm-lead">Tim sales akan konfirmasi via WhatsApp untuk metode bayar &amp; estimasi pengiriman.</p>
    <div class="confirm-block">
      <div class="confirm-row"><span class="mono">Order ID</span><strong id="builderOrderId">—</strong></div>
      <div class="confirm-row"><span class="mono">Status</span><strong style="color:var(--accent-pink)">Menunggu Konfirmasi</strong></div>
      <div class="confirm-row total"><span>Total</span><strong id="builderOrderTotal">—</strong></div>
    </div>
    <div class="confirm-foot">
      <a id="builderWaLink" href="#" target="_blank" rel="noopener" class="btn-ghost big">Chat WhatsApp</a>
      <button type="button" id="closeConfirmBuilder" class="btn-primary big">Tutup</button>
    </div>
  </div>
</div>
@endsection
@endif

@push('scripts')
<script>
(function () {
  const checkoutModal = document.getElementById('checkoutBuilderModal');
  const confirmModal  = document.getElementById('confirmBuilderModal');
  if (!checkoutModal || !confirmModal) return;

  function openModal(m) { m.classList.add('open');  m.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
  function closeModal(m){ m.classList.remove('open'); m.setAttribute('aria-hidden','true');  document.body.style.overflow=''; }

  document.getElementById('checkoutBuilderBtn')?.addEventListener('click', () => openModal(checkoutModal));
  ['closeCheckoutBuilder','closeCheckoutBuilder2'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', () => closeModal(checkoutModal));
  });
  document.getElementById('closeConfirmBuilder')?.addEventListener('click', () => {
    closeModal(confirmModal);
    window.location.href = @json(route('riwayat'));
  });
  [checkoutModal, confirmModal].forEach(m => {
    m?.addEventListener('click', (e) => { if (e.target === m) closeModal(m); });
  });

  // Pay radio highlight
  document.querySelectorAll('.pay-radio').forEach(radio => {
    radio.addEventListener('change', () => {
      document.querySelectorAll('.pay-label').forEach(l => l.classList.remove('active'));
      if (radio.checked) radio.nextElementSibling.classList.add('active');
    });
  });

  // Checkout submit → POST to /checkout → confirmation modal
  document.getElementById('builderCheckoutForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);

    for (const f of ['name','email','phone','address','city','zip']) {
      if (!(fd.get(f) || '').trim()) { alert('Mohon lengkapi semua field yang wajib.'); return; }
    }

    const items = @json($builderCart ?? []);
    const payload = {
      name:    fd.get('name'),
      email:   fd.get('email'),
      phone:   fd.get('phone'),
      company: fd.get('company') || '',
      address: fd.get('address'),
      city:    fd.get('city'),
      zip:     fd.get('zip'),
      pay:     fd.get('pay') || 'bca',
      items:   items,
    };

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    const origLbl = btn.textContent;
    btn.textContent = 'Memproses...';

    try {
      const res  = await fetch(@json(route('checkout')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });
      const json = await res.json();
      if (!json.success) throw new Error('Server returned success=false');

      document.getElementById('builderOrderId').textContent    = json.order_id;
      document.getElementById('builderOrderTotal').textContent = 'Rp ' + json.total.toLocaleString('id-ID');
      document.getElementById('builderWaLink').href            = json.wa_link;

      closeModal(checkoutModal);
      openModal(confirmModal);
    } catch (err) {
      console.error(err);
      alert('Gagal memproses pesanan. Coba lagi atau hubungi via WhatsApp.');
    } finally {
      btn.disabled = false;
      btn.textContent = origLbl;
    }
  });
})();
</script>
@endpush
