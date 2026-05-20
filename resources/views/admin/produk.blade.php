@extends('layouts.app')

@section('title', 'Admin · Produk — Si Jaring Nusantara')

@push('page-styles')
<style>
  .admin-btn { width:32px; height:32px; border-radius:6px; display:inline-grid; place-items:center;
               font-size:.85rem; font-weight:700; border:1.5px solid var(--ink); cursor:pointer; }
  .admin-btn.edit { background:var(--accent-yellow); color:var(--ink); }
  .admin-btn.edit:hover { background:var(--ink); color:var(--accent-yellow); }
  .admin-btn.del { background:#fff; color:var(--accent-pink); border-color:var(--accent-pink); }
  .admin-btn.del:hover { background:var(--accent-pink); color:#fff; }
  .row-actions { display:flex; gap:.4rem; align-items:center; }
</style>
@endpush

@section('content')
<div style="max-width:var(--container);margin:0 auto;padding:6rem 1.4rem 4rem;">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ ADMIN / KATALOG</span>
      <h1 style="margin-top:.5rem">Kelola Katalog</h1>
      <p class="lead">{{ $produk->total() }} produk total di katalog.</p>
    </div>
    <div class="user-chip">
      <span class="avi">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
      <span>{{ Auth::user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="logout">Keluar</button>
      </form>
    </div>
  </div>

  <div class="admin-nav">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a href="{{ route('admin.users') }}">Pelanggan</a>
  </div>

  <div style="display:flex;justify-content:flex-end;margin-bottom:1rem">
    <button type="button" class="btn-primary" onclick="openAddModal()">+ Tambah Produk</button>
  </div>

  <div class="table-wrap">
    <div class="table-head">
      <h3>Daftar Produk</h3>
      <span class="kbd">{{ $produk->total() }} entries</span>
    </div>
    <table class="t">
      <thead>
        <tr>
          <th>Kode</th><th>Nama</th><th>Kategori</th>
          <th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($produk as $item)
          <tr>
            <td class="oid" data-l="Kode">{{ $item->kode_produk }}</td>
            <td data-l="Nama">{{ $item->nama }}</td>
            <td data-l="Kategori">{{ $item->kategori }}</td>
            <td class="price" data-l="Harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
            <td data-l="Stok">{{ $item->stok }}</td>
            <td data-l="Status">
              <span class="status {{ $item->is_aktif ? 'done' : 'cancel' }}">
                {{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td data-l="Aksi">
              <div class="row-actions">
                <button type="button" class="admin-btn edit"
                  title="Edit"
                  data-id="{{ $item->id }}"
                  data-kode="{{ $item->kode_produk }}"
                  data-nama="{{ $item->nama }}"
                  data-spec="{{ $item->spec }}"
                  data-deskripsi="{{ $item->deskripsi }}"
                  data-kategori="{{ $item->kategori }}"
                  data-harga="{{ (int) $item->harga }}"
                  data-stok="{{ $item->stok }}"
                  data-foto="{{ $item->foto }}"
                  data-tone="{{ $item->tone }}"
                  data-aktif="{{ $item->is_aktif ? '1' : '0' }}"
                  onclick="openEditModal(this)">✎</button>
                <form method="POST" action="{{ route('admin.produk.destroy', $item) }}"
                      onsubmit="return confirm('Hapus produk {{ $item->nama }}?')"
                      style="display:inline;margin:0">
                  @csrf @method('DELETE')
                  <button type="submit" class="admin-btn del" title="Hapus">✕</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;padding:2rem">Belum ada produk.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($produk->hasPages())
    <div style="margin-top:1.2rem;display:flex;justify-content:center">{{ $produk->links() }}</div>
  @endif

</div>
@endsection

@section('modals')
<div class="modal" id="produkModal" role="dialog" aria-hidden="true">
  <div class="modal-card sm">
    <header class="modal-head">
      <div>
        <span class="kbd">⌘ PRODUK</span>
        <h3 id="modalTitle">Tambah Produk</h3>
      </div>
      <button type="button" class="icon-btn" onclick="closeProdukModal()">✕</button>
    </header>
    <form id="produkForm" method="POST" action="{{ route('admin.produk.store') }}"
          style="padding:1.2rem;display:grid;gap:1rem">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem">
        <div class="field">
          <label for="fKode">Kode Produk *</label>
          <input id="fKode" name="kode_produk" required placeholder="SW-L3-24"/>
        </div>
        <div class="field">
          <label for="fKategori">Kategori *</label>
          <select id="fKategori" name="kategori" required>
            <option value="">— Pilih —</option>
            <option value="Switch">Switch</option>
            <option value="Router">Router</option>
            <option value="Access Point">Access Point</option>
            <option value="Kabel">Kabel</option>
            <option value="Server">Server</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label for="fNama">Nama Produk *</label>
        <input id="fNama" name="nama" required placeholder="Manageable Switch 24-Port L3"/>
      </div>
      <div class="field">
        <label for="fSpec">Spesifikasi Singkat *</label>
        <input id="fSpec" name="spec" required placeholder="Gigabit · 4× SFP+ · VLAN"/>
      </div>
      <div class="field">
        <label for="fDeskripsi">Deskripsi *</label>
        <textarea id="fDeskripsi" name="deskripsi" rows="3" required></textarea>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem">
        <div class="field">
          <label for="fHarga">Harga (Rp) *</label>
          <input id="fHarga" name="harga" type="number" required min="0" placeholder="6450000"/>
        </div>
        <div class="field">
          <label for="fStok">Stok *</label>
          <input id="fStok" name="stok" type="number" required min="0" placeholder="10"/>
        </div>
      </div>
      <div class="field">
        <label for="fFoto">URL Foto</label>
        <input id="fFoto" name="foto" type="url" placeholder="https://images.unsplash.com/…"/>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem">
        <div class="field">
          <label for="fTone">Warna Background</label>
          <select id="fTone" name="tone">
            <option value="tone-pink">Pink</option>
            <option value="tone-cyan">Cyan</option>
            <option value="tone-yellow">Yellow</option>
            <option value="tone-cream" selected>Cream</option>
            <option value="tone-green">Green</option>
            <option value="tone-ink">Dark</option>
          </select>
        </div>
        <div class="field">
          <label for="fAktif">Status</label>
          <select id="fAktif" name="is_aktif">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>
      <div style="display:flex;gap:.8rem;justify-content:flex-end;padding-top:1rem;border-top:1px dashed rgba(15,14,12,.15)">
        <button type="button" class="btn-ghost" onclick="closeProdukModal()">Batal</button>
        <button type="submit" class="btn-primary" id="submitBtn">Simpan Produk</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const produkModal  = document.getElementById('produkModal');
const produkForm   = document.getElementById('produkForm');
const formMethod   = document.getElementById('formMethod');
const submitBtn    = document.getElementById('submitBtn');
const modalTitle   = document.getElementById('modalTitle');
const fKode        = document.getElementById('fKode');

function openAddModal() {
  modalTitle.textContent = 'Tambah Produk';
  submitBtn.textContent  = 'Simpan Produk';
  produkForm.action      = '{{ route("admin.produk.store") }}';
  formMethod.value       = 'POST';
  fKode.readOnly         = false;
  produkForm.reset();
  produkModal.classList.add('open');
  produkModal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function openEditModal(btn) {
  const d = btn.dataset;
  modalTitle.textContent = 'Edit Produk';
  submitBtn.textContent  = 'Simpan Perubahan';
  produkForm.action      = '/admin/produk/' + d.id;
  formMethod.value       = 'PATCH';

  fKode.value                              = d.kode;
  fKode.readOnly                           = true;
  document.getElementById('fNama').value      = d.nama;
  document.getElementById('fSpec').value      = d.spec;
  document.getElementById('fDeskripsi').value = d.deskripsi;
  document.getElementById('fKategori').value  = d.kategori;
  document.getElementById('fHarga').value     = d.harga;
  document.getElementById('fStok').value      = d.stok;
  document.getElementById('fFoto').value      = d.foto || '';
  document.getElementById('fTone').value      = d.tone;
  document.getElementById('fAktif').value     = d.aktif;

  produkModal.classList.add('open');
  produkModal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeProdukModal() {
  produkModal.classList.remove('open');
  produkModal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

produkModal.addEventListener('click', (e) => {
  if (e.target === produkModal) closeProdukModal();
});

// Auto-open if there was a validation error (server returned with input back)
@if ($errors->any())
  openAddModal();
@endif
</script>
@endpush
