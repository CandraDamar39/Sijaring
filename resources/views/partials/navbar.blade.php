@php $isAdmin = Auth::check() && Auth::user()->isAdmin(); @endphp
<header class="site-header">
  <div class="header-inner">

    <a href="{{ route('home') }}" class="logo" aria-label="SiJaring Nusantara">
      <svg viewBox="0 0 56 56" width="44" height="44" aria-hidden="true">
        <g stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none">
          <line x1="14" y1="14" x2="28" y2="28"/>
          <line x1="42" y1="14" x2="28" y2="28"/>
          <line x1="14" y1="42" x2="28" y2="28"/>
          <line x1="42" y1="42" x2="28" y2="28"/>
          <line x1="14" y1="14" x2="42" y2="14"/>
          <line x1="14" y1="42" x2="42" y2="42"/>
        </g>
        <g fill="currentColor">
          <circle cx="14" cy="14" r="4.5"/><circle cx="42" cy="14" r="4.5"/>
          <circle cx="14" cy="42" r="4.5"/><circle cx="42" cy="42" r="4.5"/>
        </g>
        <circle cx="28" cy="28" r="6" fill="var(--accent-pink)" stroke="currentColor" stroke-width="2"/>
      </svg>
      <span class="logo-text">
        <span class="logo-main">SiJaring</span>
        <span class="logo-sub">// nusantara.net</span>
      </span>
    </a>

    <nav class="main-nav" id="mainNav" aria-label="Navigasi utama">
      @if ($isAdmin)
        {{-- ADMIN NAV: no cart, no builder, no public katalog/kontak --}}
        <ul>
          <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
          <li><a href="{{ route('admin.produk') }}" class="{{ request()->routeIs('admin.produk*') ? 'active' : '' }}">Kelola Katalog</a></li>
          <li><a href="{{ route('riwayat') }}" class="{{ request()->routeIs('riwayat') ? 'active' : '' }}">Semua Pesanan</a></li>
          <li><a href="{{ route('admin.laporan') }}" class="{{ request()->routeIs('admin.laporan') ? 'active' : '' }}">Laporan</a></li>
          <li><a href="{{ route('admin.contact.settings') }}" class="{{ request()->routeIs('admin.contact*') ? 'active' : '' }}">Pengaturan Kontak</a></li>
        </ul>
      @elseif (Auth::check())
        {{-- CUSTOMER NAV --}}
        <ul>
          <li><a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') ? 'active' : '' }}">Katalog</a></li>
          <li><a href="{{ route('builder') }}" class="{{ request()->routeIs('builder*') ? 'active' : '' }}">Network Builder</a></li>
          <li><a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'active' : '' }}">Kontak</a></li>
          <li><a href="{{ route('riwayat') }}" class="{{ request()->routeIs('riwayat') ? 'active' : '' }}">Riwayat</a></li>
          <li><a href="{{ route('profil') }}" class="{{ request()->routeIs('profil') ? 'active' : '' }}">Profil</a></li>
          <li><a href="{{ route('preferensi') }}" class="{{ request()->routeIs('preferensi') ? 'active' : '' }}">Pengaturan</a></li>
        </ul>
      @else
        {{-- GUEST NAV --}}
        <ul>
          <li><a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') ? 'active' : '' }}">Katalog</a></li>
          <li><a href="{{ route('builder') }}" class="{{ request()->routeIs('builder*') ? 'active' : '' }}">Network Builder</a></li>
          <li><a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'active' : '' }}">Kontak</a></li>
        </ul>
      @endif
    </nav>

    <div class="header-cta">
      {{-- FEATURE 3: Dark mode toggle --}}
      <button id="darkToggle" type="button" aria-label="Toggle dark mode" title="Mode gelap / terang"
              style="width:38px;height:38px;border-radius:50%;border:1.5px solid var(--ink);background:var(--bg);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:all .2s;flex-shrink:0">
        <span id="darkIcon">🌙</span>
      </button>

      {{-- Cart button ONLY for non-admin on katalog --}}
      @if (!$isAdmin && request()->routeIs('katalog'))
        <button class="cart-btn" id="cartBtn" type="button" aria-label="Buka keranjang">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.5L21 8H6"/>
            <circle cx="10" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
          </svg>
          <span class="cart-count" id="cartCount" data-count="0">0</span>
        </button>
      @endif

      @auth
        <span class="nav-uname">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn-ghost" style="padding:.4rem .85rem;font-size:.82rem">Keluar</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="btn-ghost" style="padding:.45rem .9rem;font-size:.85rem">Masuk</a>
        <a href="{{ route('register') }}" class="btn-primary" style="padding:.45rem .9rem;font-size:.85rem">Daftar</a>
      @endauth
    </div>

    <button class="menu-toggle" id="menuToggle" aria-label="Buka menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
