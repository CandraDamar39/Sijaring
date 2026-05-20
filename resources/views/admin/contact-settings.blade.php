@extends('layouts.app')
@section('title', 'Pengaturan Kontak — Admin')

@section('content')
<div style="max-width:800px;margin:0 auto;padding:6rem 1.4rem 4rem">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ ADMIN / PENGATURAN</span>
      <h1 style="margin-top:.5rem">Pengaturan <span style="color:var(--accent-pink)">Kontak</span></h1>
      <p class="lead">Edit info kontak yang muncul di halaman publik (kontak, footer).</p>
    </div>
  </div>

  <div class="admin-nav">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a href="{{ route('admin.users') }}">Pelanggan</a>
  </div>

  @if (session('success'))
    <div class="flash-banner" style="margin-bottom:1.5rem">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="flash-banner error" style="margin-bottom:1.5rem">⚠ {{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.contact.update') }}">
    @csrf
    <div style="display:grid;gap:1.2rem">

      {{-- Info kontak --}}
      <div class="table-wrap" style="padding:1.6rem">
        <h3 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:1.2rem">Info Kontak</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
          <div>
            <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">No. WhatsApp (Tampil)</label>
            <input name="whatsapp" type="text" required value="{{ old('whatsapp', $settings['whatsapp'] ?? '+62 823 2927 7901') }}"
                   style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-body);font-size:.95rem"/>
          </div>
          <div>
            <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">WhatsApp Link (angka saja)</label>
            <input name="whatsapp_link" type="text" required value="{{ old('whatsapp_link', $settings['whatsapp_link'] ?? '6282329277901') }}"
                   style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-mono);font-size:.95rem"/>
          </div>
        </div>
        <div style="margin-bottom:1rem">
          <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">Email</label>
          <input name="email" type="email" required value="{{ old('email', $settings['email'] ?? 'cs@sijaring.id') }}"
                 style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-body);font-size:.95rem"/>
        </div>
        <div>
          <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">Alamat Kantor</label>
          <textarea name="address" rows="3" required
                    style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-body);font-size:.95rem">{{ old('address', $settings['address'] ?? '') }}</textarea>
        </div>
      </div>

      {{-- Jam operasional --}}
      <div class="table-wrap" style="padding:1.6rem">
        <h3 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:1.2rem">Jam Operasional</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
          @foreach (['hours_weekday' => 'Senin – Jumat', 'hours_saturday' => 'Sabtu', 'hours_sunday' => 'Minggu'] as $key => $label)
            <div>
              <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">{{ $label }}</label>
              <input name="{{ $key }}" type="text" required value="{{ old($key, $settings[$key] ?? '') }}"
                     style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-mono);font-size:.9rem"/>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Google Maps --}}
      <div class="table-wrap" style="padding:1.6rem">
        <h3 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:1.2rem">Google Maps</h3>
        <div style="display:grid;gap:1rem">
          <div>
            <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">Embed URL (iframe halaman kontak)</label>
            <input name="maps_embed_url" type="url" value="{{ old('maps_embed_url', $settings['maps_embed_url'] ?? '') }}"
                   style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-mono);font-size:.78rem"/>
          </div>
          <div>
            <label style="font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.35rem">Link "Buka di Maps"</label>
            <input name="maps_link" type="url" value="{{ old('maps_link', $settings['maps_link'] ?? '') }}"
                   style="width:100%;padding:.75rem;border:1.5px solid var(--ink);border-radius:8px;font-family:var(--font-mono);font-size:.78rem"/>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-primary" style="justify-self:start;padding:1rem 2rem;font-size:.95rem">
        Simpan Pengaturan &rarr;
      </button>

    </div>
  </form>
</div>
@endsection
