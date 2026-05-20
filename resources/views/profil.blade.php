@extends('layouts.app')

@section('title', 'Profil Saya — Si Jaring Nusantara')

@push('page-styles')
<style>
  .profile-wrap { max-width: 880px; margin: 0 auto; padding: 7rem 1.4rem 4rem; }
  .profile-tabs {
    display: flex; gap: .4rem; margin: 0 0 1.5rem;
    border-bottom: 2px solid var(--ink); overflow-x: auto;
  }
  .profile-tabs button {
    flex-shrink: 0; font-family: var(--font-mono); font-size: .82rem;
    padding: .8rem 1.2rem; border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    text-transform: uppercase; letter-spacing: .08em; color: var(--muted);
    background: transparent; border-left: none; border-right: none; border-top: none;
  }
  .profile-tabs button.active {
    border-bottom-color: var(--accent-pink);
    color: var(--ink); font-weight: 700;
  }
  .panel { display: none; }
  .panel.active { display: block; animation: fadeIn .3s ease; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }

  .profile-card {
    background: var(--bg); border: 2px solid var(--ink);
    border-radius: 14px; padding: 1.8rem 1.6rem;
    box-shadow: 6px 8px 0 0 var(--ink);
  }
  .profile-card h2 { font-family: var(--font-display); font-weight: 800; font-size: 1.5rem; margin: 0 0 1.4rem; letter-spacing: -.01em; }
  .profile-grid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
  @media (min-width: 640px) { .profile-grid { grid-template-columns: 1fr 1fr; } }
  .field-full { grid-column: 1 / -1; }
  .profile-card label {
    display: block; font-family: var(--font-mono);
    font-size: .72rem; text-transform: uppercase; letter-spacing: .1em;
    color: var(--muted); margin-bottom: .35rem;
  }
  .profile-card input, .profile-card textarea {
    width: 100%; padding: .8rem .95rem;
    border: 1.5px solid var(--ink); border-radius: 9px;
    background: #fff; font-family: var(--font-body); font-size: 1rem;
  }
  .profile-card input:focus, .profile-card textarea:focus {
    outline: none; border-color: var(--accent-pink);
    box-shadow: 0 0 0 3px rgba(255,46,126,.18);
  }
  .profile-card input[readonly] { background: var(--bg-2); color: var(--muted); cursor: not-allowed; }
  .profile-actions {
    display: flex; gap: .8rem; justify-content: flex-end;
    margin-top: 1.4rem; padding-top: 1.4rem;
    border-top: 1px dashed rgba(15,14,12,.18); flex-wrap: wrap;
  }
  .btn-save {
    background: var(--ink); color: var(--bg);
    padding: .8rem 1.4rem; border-radius: 9px;
    font-family: var(--font-display); font-weight: 700;
    letter-spacing: .04em; text-transform: uppercase; font-size: .85rem;
    border: 1.5px solid var(--ink); transition: all .2s;
  }
  .btn-save:hover { background: var(--accent-pink); transform: translate(-2px,-2px); box-shadow: 4px 4px 0 0 var(--ink); }

  .avatar-row {
    display: flex; align-items: center; gap: 1.2rem;
    margin-bottom: 1.6rem; padding-bottom: 1.4rem;
    border-bottom: 1px dashed rgba(15,14,12,.18);
  }
  .avatar-big {
    width: 80px; height: 80px; border-radius: 50%;
    background: var(--accent-pink); color: #fff;
    border: 2.5px solid var(--ink); box-shadow: 4px 4px 0 var(--ink);
    display: grid; place-items: center;
    font-family: var(--font-display); font-size: 1.8rem; font-weight: 800;
  }
  .avatar-row .label { font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
  .avatar-row .val { font-size: 1.3rem; font-weight: 700; margin-top: .1rem; }
  .avatar-row .small { font-family: var(--font-mono); font-size: .78rem; color: var(--muted); margin-top: .2rem; }

  .save-ok {
    background: rgba(31,168,91,.12); border: 1px solid var(--accent-green);
    color: #156d3a; padding: .7rem .9rem; border-radius: 8px;
    font-family: var(--font-mono); font-size: .82rem; margin-bottom: 1rem;
  }
  .error-msg {
    background: rgba(255,46,126,.1); border: 1px solid var(--accent-pink);
    color: var(--accent-pink); padding: .6rem .8rem; border-radius: 8px;
    font-size: .85rem; margin: 0 0 1rem; font-family: var(--font-mono);
  }
</style>
@endpush

@section('content')
<div class="profile-wrap">

  <div class="page-head">
    <div>
      <span class="kbd">⌘ PROFIL.06</span>
      <h1 style="margin-top:.5rem">Profil saya.</h1>
      <p class="lead">Edit data pelanggan, alamat pengiriman &amp; keamanan akun.</p>
    </div>
  </div>

  <div class="profile-tabs" role="tablist">
    <button type="button" data-tab="profile"
            class="{{ session('active_tab', 'profile') === 'profile' ? 'active' : '' }}">Data Pelanggan</button>
    <button type="button" data-tab="address"
            class="{{ session('active_tab') === 'address' ? 'active' : '' }}">Alamat Pengiriman</button>
    <button type="button" data-tab="security"
            class="{{ session('active_tab') === 'security' ? 'active' : '' }}">Keamanan</button>
  </div>

  @if (session('success'))
    <div class="save-ok">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="error-msg">⚠ {{ $errors->first() }}</div>
  @endif

  {{-- TAB 1: Data Pribadi --}}
  <section class="panel {{ session('active_tab', 'profile') === 'profile' ? 'active' : '' }}" id="panel-profile">
    <div class="profile-card">
      <div class="avatar-row">
        <div class="avatar-big" id="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
          <div class="label">Pelanggan sejak</div>
          <div class="val">{{ $user->created_at->translatedFormat('d F Y') }}</div>
          <div class="small">{{ $user->orders()->count() }} transaksi</div>
        </div>
      </div>

      <h2>Data Pribadi</h2>
      <form method="POST" action="{{ route('profil.profile') }}">
        @csrf
        <div class="profile-grid">
          <div>
            <label for="pName">Nama Lengkap</label>
            <input id="pName" name="name" required value="{{ old('name', $user->name) }}"/>
          </div>
          <div>
            <label for="pEmail">Email</label>
            <input id="pEmail" type="email" value="{{ $user->email }}" readonly/>
          </div>
          <div>
            <label for="pPhone">No. WhatsApp</label>
            <input id="pPhone" name="phone" type="tel" required value="{{ old('phone', $user->phone) }}"/>
          </div>
          <div>
            <label for="pCompany">Perusahaan / Instansi</label>
            <input id="pCompany" name="company" placeholder="opsional" value="{{ old('company', $user->company) }}"/>
          </div>
          <div class="field-full">
            <label for="pBio">Catatan tambahan</label>
            <textarea id="pBio" name="bio" rows="3" placeholder="Mis. preferensi pengiriman, NPWP, dll.">{{ old('bio', $user->bio) }}</textarea>
          </div>
        </div>
        <div class="profile-actions">
          <a href="{{ route('home') }}" class="btn-ghost">Batal</a>
          <button type="submit" class="btn-save">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </section>

  {{-- TAB 2: Alamat --}}
  <section class="panel {{ session('active_tab') === 'address' ? 'active' : '' }}" id="panel-address">
    <div class="profile-card">
      <h2>Alamat Pengiriman Utama</h2>
      <form method="POST" action="{{ route('profil.address') }}">
        @csrf
        <div class="profile-grid">
          <div class="field-full">
            <label for="aAddr">Alamat Lengkap</label>
            <textarea id="aAddr" name="address" rows="3" required placeholder="Jalan, no., RT/RW, kelurahan…">{{ old('address', $user->address) }}</textarea>
          </div>
          <div>
            <label for="aCity">Kota / Kabupaten</label>
            <input id="aCity" name="city" required value="{{ old('city', $user->city ?? 'Jember') }}"/>
          </div>
          <div>
            <label for="aProv">Provinsi</label>
            <input id="aProv" name="province" required value="{{ old('province', $user->province ?? 'Jawa Timur') }}"/>
          </div>
          <div>
            <label for="aZip">Kode Pos</label>
            <input id="aZip" name="zip" required inputmode="numeric" value="{{ old('zip', $user->zip ?? '68122') }}"/>
          </div>
          <div>
            <label for="aLabel">Label Alamat</label>
            <input id="aLabel" name="address_label" value="{{ old('address_label', $user->address_label ?? 'Rumah') }}" placeholder="Kantor / Rumah / Gudang"/>
          </div>
        </div>
        <div class="profile-actions">
          <button type="reset" class="btn-ghost">Batal</button>
          <button type="submit" class="btn-save">Simpan Alamat</button>
        </div>
      </form>
    </div>
  </section>

  {{-- TAB 3: Keamanan --}}
  <section class="panel {{ session('active_tab') === 'security' ? 'active' : '' }}" id="panel-security">
    <div class="profile-card">
      <h2>Ubah Kata Sandi</h2>
      <form method="POST" action="{{ route('profil.password') }}">
        @csrf
        <div class="profile-grid">
          <div class="field-full">
            <label for="pwOld">Kata Sandi Lama</label>
            <input id="pwOld" name="old" type="password" required/>
          </div>
          <div>
            <label for="pwNew">Kata Sandi Baru</label>
            <input id="pwNew" name="new" type="password" required/>
          </div>
          <div>
            <label for="pwConf">Konfirmasi</label>
            <input id="pwConf" name="conf" type="password" required/>
          </div>
        </div>
        <div class="profile-actions">
          <button type="reset" class="btn-ghost">Batal</button>
          <button type="submit" class="btn-save">Ubah Sandi</button>
        </div>
      </form>

      <h2 style="margin-top:2.2rem;color:#b3204e">Zona Bahaya</h2>
      <p style="color:var(--muted);font-size:.92rem;margin:0 0 1rem">
        Hapus akun beserta seluruh riwayat pembelian. Aksi ini tidak bisa dibatalkan.
      </p>
      <form method="POST" action="{{ route('profil.destroy') }}"
            onsubmit="return confirm('Yakin mau hapus akun? Semua riwayat juga akan dihapus.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger">Hapus Akun Saya</button>
      </form>
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script>
  // Tab switcher
  document.querySelectorAll('.profile-tabs button').forEach(b => {
    b.addEventListener('click', () => {
      document.querySelectorAll('.profile-tabs button').forEach(x => x.classList.remove('active'));
      document.querySelectorAll('.panel').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      document.getElementById('panel-' + b.dataset.tab).classList.add('active');
    });
  });

  // Avatar auto-update saat nama berubah
  const nameInput = document.getElementById('pName');
  const avatar    = document.getElementById('avatar');
  if (nameInput && avatar) {
    nameInput.addEventListener('input', () => {
      avatar.textContent = (nameInput.value.trim()[0] || 'U').toUpperCase();
    });
  }
</script>
@endpush
