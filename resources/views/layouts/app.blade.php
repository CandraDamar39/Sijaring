<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- ═══ FEATURE 3: Cookie helpers + anti-flash theme (HARUS paling awal) ═══ --}}
  <script>
  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = name + '=' + encodeURIComponent(value) +
      ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
  }
  function getCookie(name) {
    const key = name + '=';
    const decoded = decodeURIComponent(document.cookie);
    for (let p of decoded.split(';')) {
      p = p.trim();
      if (p.startsWith(key)) return p.substring(key.length);
    }
    return null;
  }
  function deleteCookie(name) { setCookie(name, '', -1); }

  // Terapkan tema SEGERA untuk mencegah flash (FOUC)
  (function applyThemeFromCookie() {
    const theme    = getCookie('sj_theme') || 'light';
    const fontSize = getCookie('sj_fontsize') || 'normal';
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else if (theme === 'system') {
      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
      }
    }
    const fontSizeMap = { small: '14px', normal: '16px', large: '18px' };
    document.documentElement.style.fontSize = fontSizeMap[fontSize] || '16px';
  })();
  </script>
  <title>@yield('title', 'CV. SiJaring Nusantara')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @stack('page-styles')
  @stack('styles')
</head>
<body style="display:flex;flex-direction:column;min-height:100vh">

  @include('partials.navbar')

  @if (session('success'))
    <div class="flash-banner">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="flash-banner error">{{ session('error') }}</div>
  @endif

  <main style="flex:1">
    @yield('content')
  </main>

  @include('partials.footer')

  @yield('modals')

  <script src="{{ asset('js/app.js') }}" defer></script>

  {{-- ═══ FEATURE 3: Dark Mode Toggle ═══ --}}
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('darkToggle');
    const darkIcon  = document.getElementById('darkIcon');

    function updateIcon() {
      const isDark = document.documentElement.classList.contains('dark');
      if (darkIcon) darkIcon.textContent = isDark ? '☀️' : '🌙';
    }

    toggleBtn?.addEventListener('click', () => {
      const isDark = document.documentElement.classList.toggle('dark');
      setCookie('sj_theme', isDark ? 'dark' : 'light', 365);
      updateIcon();
    });

    updateIcon();
  });
  </script>

  @stack('scripts')

</body>
</html>
