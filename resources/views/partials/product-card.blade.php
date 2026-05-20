<article class="product" data-id="{{ $p->kode_produk }}" data-kat="{{ $p->kategori }}">
  <div class="product-media {{ $p->tone ?? 'tone-cream' }}">
    @if ($p->foto)
      <img src="{{ $p->foto }}" alt="{{ $p->nama }}" loading="lazy">
    @endif
  </div>
  <div class="product-body">
    <h3>{{ $p->nama }}</h3>
    <p class="spec">{{ $p->spec }} · stok: {{ $p->stok }}</p>
    <div class="price-row">
      <span class="price">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
      <button class="btn-buy"
        data-add
        data-id="{{ $p->kode_produk }}"
        data-name="{{ $p->nama }}"
        data-spec="{{ $p->spec }}"
        data-price="{{ (int) $p->harga }}">+ Keranjang</button>
    </div>
  </div>
</article>
