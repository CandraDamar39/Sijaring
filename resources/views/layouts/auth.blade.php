<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>@yield('title', 'Si Jaring Nusantara')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}"/>
  <style>
    /* Auth page: full-bleed, centered, no header/footer chrome */
    body.auth-body {
      min-height: 100vh;
      margin: 0;
      display: grid;
      grid-template-rows: auto 1fr;
      background: var(--bg);
      background-image:
        radial-gradient(ellipse at 15% 15%, rgba(255,46,126,.08) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 85%, rgba(0,212,224,.08) 0%, transparent 55%),
        radial-gradient(circle at 1px 1px, rgba(15,14,12,.07) 1px, transparent 0);
      background-size: auto, auto, 22px 22px;
    }
    .auth-header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 1.2rem 2rem;
      border-bottom: 2px solid var(--ink);
      background: rgba(245,240,230,.7);
      backdrop-filter: blur(8px);
    }
    .auth-header .brand {
      display: inline-flex; align-items: center; gap: .6rem;
      font-family: var(--font-display); font-weight: 700; font-size: 1.1rem;
      color: var(--ink);
    }
    .auth-header .ml { font-family: var(--font-mono); font-size: .85rem; color: var(--muted); margin: 0; }
    .auth-header .ml a { color: var(--accent-pink); font-weight: 700; text-decoration: underline; }
    .auth-wrap {
      display: grid; place-items: center;
      padding: 3rem 1.2rem;
    }
    .auth-card {
      width: 100%; max-width: 460px;
      background: var(--bg); border: 2px solid var(--ink);
      border-radius: 14px; padding: 2.4rem 2rem;
      box-shadow: 8px 10px 0 0 var(--ink);
    }
  </style>
</head>
<body class="auth-body">

<header class="auth-header">
  <a href="{{ route('home') }}" class="brand">
    <svg viewBox="0 0 56 56" width="36" height="36" aria-hidden="true">
      <g stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none">
        <line x1="14" y1="14" x2="28" y2="28"/><line x1="42" y1="14" x2="28" y2="28"/>
        <line x1="14" y1="42" x2="28" y2="28"/><line x1="42" y1="42" x2="28" y2="28"/>
      </g>
      <g fill="currentColor">
        <circle cx="14" cy="14" r="4"/><circle cx="42" cy="14" r="4"/>
        <circle cx="14" cy="42" r="4"/><circle cx="42" cy="42" r="4"/>
      </g>
      <circle cx="28" cy="28" r="6" fill="#ff2e7e" stroke="currentColor" stroke-width="2"/>
    </svg>
    <span>Si Jaring</span>
  </a>
  <p class="ml">@yield('auth-header-link')</p>
</header>

<main class="auth-wrap">
  <section class="auth-card">
    <span class="kbd">@yield('auth-badge', '⌘ AUTH')</span>
    <h1 class="auth-title">@yield('auth-title')</h1>
    <p class="auth-lead">@yield('auth-lead')</p>
    @yield('content')
  </section>
</main>

</body>
</html>
