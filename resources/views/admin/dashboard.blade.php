@extends('layouts.app')

@section('title', 'Admin Dashboard — Si Jaring Nusantara')

@section('content')
<div style="max-width:var(--container);margin:0 auto;padding:6rem 1.4rem 4rem;">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ ADMIN</span>
      <h1 style="margin-top:.5rem">Admin Panel</h1>
      <p class="lead">Kelola produk, pengguna, dan data Si Jaring.</p>
    </div>
    <div class="user-chip">
      <span class="avi">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
      <span class="uname">{{ Auth::user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="logout">Keluar</button>
      </form>
    </div>
  </div>

  <div class="admin-nav">
    <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
    <a href="{{ route('admin.users') }}">Pelanggan</a>
  </div>

  <div class="stat-grid">
    <x-stat-card judul="Total Produk"    :nilai="$stats['total_produk']"   warna="pink" />
    <x-stat-card judul="Produk Aktif"    :nilai="$stats['produk_aktif']"   warna="cyan" />
    <x-stat-card judul="Total Pelanggan" :nilai="$stats['total_user']"     warna="yellow" />
    <x-stat-card judul="Kategori"        :nilai="$stats['total_kategori']" />
  </div>

  <div class="table-wrap">
    <div class="table-head">
      <h3>Produk Terbaru</h3>
      <a href="{{ route('admin.produk') }}" class="kbd">Lihat Semua</a>
    </div>
    <table class="t">
      <thead>
        <tr>
          <th>Kode</th><th>Nama</th><th>Kategori</th>
          <th>Harga</th><th>Stok</th><th>Status</th>
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
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:2rem">Belum ada produk.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
