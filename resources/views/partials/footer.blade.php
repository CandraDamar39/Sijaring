@php
  // Pull contact info from DB so admin edits propagate everywhere
  $cs = \App\Models\ContactSetting::all()->pluck('value', 'key');
@endphp
<footer class="site-footer" id="kontak">
  <div class="footer-grid">

    <div class="footer-brand">
      <svg viewBox="0 0 56 56" width="36" height="36" aria-hidden="true">
        <g stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none">
          <line x1="14" y1="14" x2="28" y2="28"/><line x1="42" y1="14" x2="28" y2="28"/>
          <line x1="14" y1="42" x2="28" y2="28"/><line x1="42" y1="42" x2="28" y2="28"/>
        </g>
        <g fill="currentColor">
          <circle cx="14" cy="14" r="4.5"/><circle cx="42" cy="14" r="4.5"/>
          <circle cx="14" cy="42" r="4.5"/><circle cx="42" cy="42" r="4.5"/>
        </g>
        <circle cx="28" cy="28" r="6" fill="var(--accent-pink)" stroke="currentColor" stroke-width="2"/>
      </svg>
      <p class="footer-tag">// Infrastruktur jaringan terpercaya<br>untuk bisnis Indonesia sejak 2024.</p>
    </div>

    <div>
      <h4>Halaman</h4>
      <ul class="footer-list links">
        <li><a href="{{ route('katalog') }}">Katalog Produk</a></li>
        <li><a href="{{ route('builder') }}">Network Builder</a></li>
        <li><a href="{{ route('kontak') }}">Kontak Kami</a></li>
        @auth
          @if (Auth::user()->isAdmin())
            <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
          @endif
        @endauth
      </ul>
    </div>

    <div>
      <h4>Jam Operasional</h4>
      <ul class="footer-list">
        <li>Senin – Jumat <span>{{ $cs['hours_weekday'] ?? '08.00 – 18.00' }}</span></li>
        <li>Sabtu <span>{{ $cs['hours_saturday'] ?? '09.00 – 15.00' }}</span></li>
        <li>Minggu <span>{{ $cs['hours_sunday'] ?? 'Tutup' }}</span></li>
      </ul>
    </div>

    <div>
      <h4>Kontak</h4>
      <ul class="footer-list">
        <li>{{ $cs['email'] ?? 'cs@sijaring.id' }}</li>
        <li>{{ $cs['whatsapp'] ?? '+62 823 2927 7901' }}</li>
        <li style="font-size:.85rem;line-height:1.5;color:#b8b3a4;">
          {!! nl2br(e($cs['address'] ?? 'Jl. Panjaitan Blok F, Gg. Sebelah Alfamart No. 108, RW.26 Lingk. Sadengan, Kebonsari, Kec. Sumbersari, Kab. Jember, Jawa Timur 68122')) !!}
        </li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <span>© {{ date('Y') }} CV. SiJaring Nusantara — Laravel {{ app()->version() }}.</span>
    <span class="mono">tugas 6 // status: <em class="green">operational</em></span>
  </div>
</footer>
