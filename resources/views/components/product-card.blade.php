@props([
    'kode',
    'nama',
    'harga',
    'kategori',
    'stok',
    'deskripsi' => '',
    'foto'      => null,
])

<div class="product-card">
  <div class="product-badge">{{ $kategori }}</div>
  <h3 class="product-nama">{{ $nama }}</h3>
  <p class="product-kode">{{ $kode }}</p>
  <p class="product-desc">{{ $deskripsi }}</p>
  <div class="product-footer">
    <span class="product-harga">Rp {{ number_format($harga, 0, ',', '.') }}</span>
    <span class="product-stok">Stok: {{ $stok }}</span>
  </div>
</div>
