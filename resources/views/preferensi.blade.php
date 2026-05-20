@extends('layouts.app')

@section('title', 'Preferensi — Si Jaring Nusantara')

@section('content')
<div style="max-width:600px;margin:0 auto;padding:6rem 1.4rem 4rem">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ PREFERENSI</span>
      <h1 style="margin-top:.5rem">Pengaturan<br><span style="color:var(--accent-pink)">Tampilan</span></h1>
      <p class="lead">Pilih tema dan ukuran teks yang nyaman untuk Anda.</p>
    </div>
  </div>

  {{-- Success toast --}}
  <div id="prefToast" hidden
       style="background:rgba(31,168,91,.12);border:1px solid var(--accent-green);color:#156d3a;padding:.8rem 1rem;border-radius:8px;margin-bottom:1.4rem;font-family:var(--font-mono);font-size:.85rem">
    ✓ Preferensi berhasil disimpan!
  </div>

  <form id="prefForm" novalidate>
    @csrf

    {{-- Theme Selector --}}
    <div style="margin-bottom:1.6rem">
      <label style="font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.8rem">
        Tema Tampilan
      </label>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.8rem">
        @foreach (['light' => ['label' => 'Light', 'icon' => '☀️', 'desc' => 'Terang'], 'dark' => ['label' => 'Dark', 'icon' => '🌙', 'desc' => 'Gelap'], 'system' => ['label' => 'System', 'icon' => '💻', 'desc' => 'Ikuti sistem']] as $val => $opt)
        <label style="cursor:pointer">
          <input type="radio" name="theme" value="{{ $val }}" {{ $theme === $val ? 'checked' : '' }} style="display:none" class="theme-radio">
          <div class="theme-card"
               style="border:2px solid {{ $theme === $val ? 'var(--accent-pink)' : 'var(--ink)' }};border-radius:12px;padding:1rem;text-align:center;transition:all .2s;background:var(--bg);box-shadow:{{ $theme === $val ? '4px 4px 0 0 var(--accent-pink)' : '4px 4px 0 0 var(--ink)' }}">
            <div style="font-size:1.8rem;margin-bottom:.3rem">{{ $opt['icon'] }}</div>
            <div style="font-family:var(--font-display);font-weight:700;font-size:.9rem">{{ $opt['label'] }}</div>
            <div style="font-family:var(--font-mono);font-size:.7rem;color:var(--muted);margin-top:.2rem">{{ $opt['desc'] }}</div>
          </div>
        </label>
        @endforeach
      </div>
    </div>

    {{-- Font Size --}}
    <div style="margin-bottom:2rem">
      <label style="font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);display:block;margin-bottom:.8rem">
        Ukuran Teks
      </label>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.8rem">
        @foreach (['small' => ['label' => 'Kecil', 'size' => '14px'], 'normal' => ['label' => 'Normal', 'size' => '16px'], 'large' => ['label' => 'Besar', 'size' => '18px']] as $val => $opt)
        <label style="cursor:pointer">
          <input type="radio" name="fontsize" value="{{ $val }}" {{ $fontSize === $val ? 'checked' : '' }} style="display:none" class="font-radio">
          <div class="font-card"
               style="border:2px solid {{ $fontSize === $val ? 'var(--accent-cyan)' : 'var(--ink)' }};border-radius:12px;padding:1rem;text-align:center;transition:all .2s;background:var(--bg);box-shadow:{{ $fontSize === $val ? '4px 4px 0 0 var(--accent-cyan)' : '4px 4px 0 0 var(--ink)' }}">
            <div style="font-size:{{ $opt['size'] }};font-weight:700;margin-bottom:.3rem;font-family:var(--font-display)">Aa</div>
            <div style="font-family:var(--font-mono);font-size:.75rem">{{ $opt['label'] }}</div>
            <div style="font-family:var(--font-mono);font-size:.65rem;color:var(--muted)">{{ $opt['size'] }}</div>
          </div>
        </label>
        @endforeach
      </div>
    </div>

    <button type="submit" id="prefSubmit"
            style="width:100%;background:var(--ink);color:var(--bg);padding:1rem;border-radius:10px;font-family:var(--font-display);font-weight:700;letter-spacing:.04em;text-transform:uppercase;font-size:.9rem;border:1.5px solid var(--ink);cursor:pointer;transition:all .2s">
      Simpan Preferensi →
    </button>
  </form>
</div>
@endsection

@push('scripts')
<script>
/* ═══ FEATURE 3: Preferensi form — Fetch POST ═══ */

// Highlight pilihan saat radio berubah
document.querySelectorAll('.theme-radio').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('.theme-card').forEach(c => {
      c.style.borderColor = 'var(--ink)';
      c.style.boxShadow   = '4px 4px 0 0 var(--ink)';
    });
    if (r.checked) {
      const card = r.nextElementSibling;
      card.style.borderColor = 'var(--accent-pink)';
      card.style.boxShadow   = '4px 4px 0 0 var(--accent-pink)';
    }
  });
});

document.querySelectorAll('.font-radio').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('.font-card').forEach(c => {
      c.style.borderColor = 'var(--ink)';
      c.style.boxShadow   = '4px 4px 0 0 var(--ink)';
    });
    if (r.checked) {
      const card = r.nextElementSibling;
      card.style.borderColor = 'var(--accent-cyan)';
      card.style.boxShadow   = '4px 4px 0 0 var(--accent-cyan)';
    }
  });
});

document.getElementById('prefForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const submitBtn = document.getElementById('prefSubmit');

  submitBtn.textContent = 'Menyimpan...';
  submitBtn.disabled    = true;

  try {
    const res = await fetch(@json(route('preferensi.save')), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        theme:    fd.get('theme'),
        fontsize: fd.get('fontsize'),
      }),
    });

    const json = await res.json();
    if (!json.success) throw new Error('Server error');

    const theme    = json.theme;
    const fontSize = json.fontsize;

    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
      setCookie('sj_theme', 'dark', 365);
    } else if (theme === 'system') {
      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
      } else {
        document.documentElement.classList.remove('dark');
      }
      setCookie('sj_theme', 'system', 365);
    } else {
      document.documentElement.classList.remove('dark');
      setCookie('sj_theme', 'light', 365);
    }

    const fontSizeMap = { small: '14px', normal: '16px', large: '18px' };
    document.documentElement.style.fontSize = fontSizeMap[fontSize] || '16px';
    setCookie('sj_fontsize', fontSize, 365);

    const icon = document.getElementById('darkIcon');
    if (icon) {
      icon.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
    }

    const toast = document.getElementById('prefToast');
    toast.hidden = false;
    setTimeout(() => { toast.hidden = true; }, 3000);
  } catch (err) {
    alert('Gagal menyimpan preferensi. Coba lagi.');
  } finally {
    submitBtn.textContent = 'Simpan Preferensi →';
    submitBtn.disabled    = false;
  }
});
</script>
@endpush
