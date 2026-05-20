@extends('layouts.app')

@section('title', 'Laporan Penjualan — Admin')

@push('page-styles')
<style>
  .laporan-grid { display: grid; gap: 1.5rem; }
  .chart-card { background: var(--bg); border: 2px solid var(--ink); border-radius: 14px; box-shadow: 6px 8px 0 0 var(--ink); padding: 1.6rem; }
  .chart-card h3 { font-family: var(--font-display); font-size: 1.1rem; margin: 0 0 1.2rem; }

  /* Horizontal bar chart */
  .bar-row { display: grid; grid-template-columns: 130px 1fr auto; gap: .8rem; align-items: center; margin-bottom: .7rem; }
  .bar-row .bar-label { font-family: var(--font-mono); font-size: .78rem; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .bar-track { background: var(--bg-2); border: 1.5px solid var(--ink); border-radius: 999px; height: 22px; overflow: hidden; }
  .bar-fill { height: 100%; border-radius: 999px; transition: width .6s var(--ease); min-width: 2px; }
  .bar-fill.pink { background: var(--accent-pink); }
  .bar-fill.cyan { background: var(--accent-cyan); }
  .bar-fill.ink  { background: var(--ink); }
  .bar-val { font-family: var(--font-mono); font-size: .8rem; font-weight: 700; white-space: nowrap; }

  /* Vertical column chart (months) */
  .col-chart { display: flex; align-items: flex-end; gap: .8rem; height: 200px; padding-top: 1rem; border-bottom: 2px solid var(--ink); }
  .col-item { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: .5rem; }
  .col-bar { width: 100%; max-width: 56px; background: var(--accent-pink); border: 1.5px solid var(--ink); border-radius: 8px 8px 0 0; transition: height .6s var(--ease); min-height: 4px; }
  .col-label { font-family: var(--font-mono); font-size: .68rem; color: var(--muted); text-align: center; }
  .col-amount { font-family: var(--font-mono); font-size: .62rem; color: var(--ink); font-weight: 700; }

  @media (min-width: 900px) { .laporan-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; } }
</style>
@endpush

@section('content')
<div style="max-width:var(--container);margin:0 auto;padding:6rem 1.4rem 4rem;">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ ADMIN / LAPORAN</span>
      <h1 style="margin-top:.5rem">Laporan <span style="color:var(--accent-pink)">Penjualan</span></h1>
      <p class="lead">Ringkasan keuangan &amp; analisis penjualan Si Jaring.</p>
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

  {{-- Stat cards --}}
  <div class="stat-grid" style="max-width:none">
    <div class="stat-card pink">
      <div class="lbl">Total Pendapatan</div>
      <div class="num" style="font-size:1.6rem">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
      <div class="stat-sub" style="font-family:var(--font-mono);font-size:.7rem;color:var(--muted);margin-top:.3rem">dari pesanan valid</div>
    </div>
    <div class="stat-card cyan">
      <div class="lbl">Pesanan Valid</div>
      <div class="num">{{ $totalValid }}</div>
      <div class="stat-sub" style="font-family:var(--font-mono);font-size:.7rem;color:var(--muted);margin-top:.3rem">dari {{ $totalOrders }} total</div>
    </div>
    <div class="stat-card yellow">
      <div class="lbl">Rata-rata / Pesanan</div>
      <div class="num" style="font-size:1.6rem">Rp {{ number_format($avgOrder, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
      <div class="lbl">Unit Terjual</div>
      <div class="num">{{ $unitsSold }}</div>
      <div class="stat-sub" style="font-family:var(--font-mono);font-size:.7rem;color:var(--muted);margin-top:.3rem">total qty produk</div>
    </div>
  </div>

  {{-- Monthly revenue column chart --}}
  <div class="chart-card" style="margin-top:1.5rem">
    <h3>Pendapatan 6 Bulan Terakhir</h3>
    <div class="col-chart">
      @foreach ($months as $m)
        <div class="col-item">
          <span class="col-amount">{{ $m['value'] > 0 ? 'Rp ' . number_format($m['value'] / 1000, 0, ',', '.') . 'rb' : '–' }}</span>
          <div class="col-bar" style="height: {{ max(2, round($m['value'] / $maxMonth * 100)) }}%"></div>
          <span class="col-label">{{ $m['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  <div class="laporan-2col" style="margin-top:1.5rem">

    {{-- Pesanan per status --}}
    <div class="chart-card">
      <h3>Pesanan per Status</h3>
      @foreach ($byStatus as $status => $count)
        @php
          $cls = match ($status) {
            'Selesai'    => 'cyan',
            'Dibatalkan' => 'ink',
            default      => 'pink',
          };
        @endphp
        <div class="bar-row">
          <span class="bar-label" title="{{ $status }}">{{ $status }}</span>
          <div class="bar-track">
            <div class="bar-fill {{ $cls }}" style="width: {{ round($count / $maxStatus * 100) }}%"></div>
          </div>
          <span class="bar-val">{{ $count }}</span>
        </div>
      @endforeach
    </div>

    {{-- Produk terlaris --}}
    <div class="chart-card">
      <h3>Produk Terlaris</h3>
      @forelse ($topProducts as $p)
        <div class="bar-row">
          <span class="bar-label" title="{{ $p->produk_name }}">{{ $p->produk_name }}</span>
          <div class="bar-track">
            <div class="bar-fill pink" style="width: {{ round($p->total_qty / $maxQty * 100) }}%"></div>
          </div>
          <span class="bar-val">{{ $p->total_qty }}×</span>
        </div>
      @empty
        <p style="font-family:var(--font-mono);font-size:.85rem;color:var(--muted)">// Belum ada penjualan.</p>
      @endforelse
    </div>

  </div>

  {{-- Detail tabel produk terlaris --}}
  @if ($topProducts->isNotEmpty())
    <div class="table-wrap" style="margin-top:1.5rem">
      <div class="table-head"><h3>Rincian Produk Terlaris</h3></div>
      <table class="t">
        <thead><tr><th>Produk</th><th>Qty Terjual</th><th>Total Pendapatan</th></tr></thead>
        <tbody>
          @foreach ($topProducts as $p)
            <tr>
              <td data-l="Produk">{{ $p->produk_name }}</td>
              <td data-l="Qty">{{ $p->total_qty }} unit</td>
              <td class="price" data-l="Pendapatan">Rp {{ number_format($p->total_rev, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>
@endsection
