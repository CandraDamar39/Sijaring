@extends('layouts.app')

@section('title', ($isAdmin ? 'Semua Pesanan' : 'Riwayat Pesanan') . ' — Si Jaring Nusantara')

@section('content')
<div style="max-width:var(--container);margin:0 auto;padding:6rem 1.4rem 4rem;">

  <div class="page-head">
    <div>
      <span class="kbd">⌘ {{ $isAdmin ? 'ADMIN / PESANAN' : 'RIWAYAT' }}</span>
      <h1>{{ $isAdmin ? 'Semua' : 'Riwayat' }}<br><span class="text-pink">Pesanan</span></h1>
      <p class="lead">
        @if ($isAdmin)
          Daftar semua pesanan dari seluruh pelanggan ({{ method_exists($orders, 'total') ? $orders->total() : $orders->count() }} total).
        @else
          Semua pesanan yang pernah Anda lakukan ({{ $orders->count() }} total).
        @endif
      </p>
    </div>
    @if (!$isAdmin)
      <a href="{{ route('katalog') }}" class="btn-primary">Belanja Lagi &rarr;</a>
    @endif
  </div>

  @php
    $isEmpty = method_exists($orders, 'total') ? $orders->total() === 0 : $orders->isEmpty();
  @endphp

  @if ($isEmpty)
    <div class="empty-state">
      <div class="ic">📦</div>
      <h3>Belum ada pesanan{{ $isAdmin ? ' masuk' : '' }}</h3>
      <p>{{ $isAdmin ? 'Belum ada pelanggan yang melakukan pemesanan.' : 'Mulai belanja dari katalog produk kami.' }}</p>
      @if (!$isAdmin)
        <a href="{{ route('katalog') }}" class="btn">Lihat Katalog</a>
      @endif
    </div>
  @else
    <div class="table-wrap">
      <div class="table-head">
        <h3>{{ $isAdmin ? 'Semua Pesanan' : 'Pesanan Saya' }}</h3>
        <span class="kbd">{{ method_exists($orders, 'total') ? $orders->total() : $orders->count() }} pesanan</span>
      </div>
      <table class="t">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Tanggal</th>
            @if ($isAdmin)
              <th>Pelanggan</th>
            @endif
            <th>Item</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($orders as $order)
            @php
              $statusClass = [
                'Menunggu Konfirmasi' => 'pending',
                'Diproses'            => 'process',
                'Dikirim'             => 'process',
                'Selesai'             => 'done',
                'Dibatalkan'          => 'cancel',
              ][$order->status] ?? 'pending';
              $payInfo = [
                'paid'      => ['💳 Lunas', 'done'],
                'pending'   => ['Menunggu Pembayaran', 'pending'],
                'failed'    => ['Pembayaran Gagal', 'cancel'],
                'expired'   => ['Kedaluwarsa', 'cancel'],
                'challenge' => ['Perlu Review', 'pending'],
              ][$order->payment_status] ?? ['—', 'pending'];
            @endphp
            <tr>
              <td class="oid" data-l="Order ID">{{ $order->order_id }}</td>
              <td data-l="Tanggal">{{ $order->created_at->translatedFormat('d M Y H:i') }}</td>
              @if ($isAdmin)
                <td data-l="Pelanggan">
                  <strong style="font-size:.92rem">{{ $order->name }}</strong><br>
                  <span style="font-family:var(--font-mono);font-size:.72rem;color:var(--muted)">
                    {{ $order->email }}
                  </span>
                </td>
              @endif
              <td data-l="Item">
                <ul class="items-mini">
                  @foreach ($order->items as $item)
                    <li>{{ $item->produk_name }} ×{{ $item->qty }}</li>
                  @endforeach
                </ul>
              </td>
              <td class="price" data-l="Total">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
              @if ($isAdmin)
                <td data-l="Status">
                  <form method="POST" action="{{ route('admin.orders.status', $order) }}" style="display:inline;margin:0">
                    @csrf @method('PATCH')
                    <select name="status" onchange="this.form.submit()"
                            style="font-family:var(--font-mono);font-size:.75rem;padding:.35rem .55rem;border-radius:6px;border:1.5px solid var(--ink);cursor:pointer;background:var(--bg)">
                      @foreach (['Menunggu Konfirmasi','Diproses','Dikirim','Selesai','Dibatalkan'] as $s)
                        <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                      @endforeach
                    </select>
                  </form>
                  <div style="margin-top:.5rem">
                    <span class="status {{ $payInfo[1] }}" style="font-size:.66rem">{{ $payInfo[0] }}</span>
                    @if ($order->payment_status === 'pending')
                      <button type="button" class="btn-cek-status" data-order="{{ $order->order_id }}"
                              style="margin-left:.4rem;font-family:var(--font-mono);font-size:.66rem;padding:.25rem .55rem;border:1.5px solid var(--ink);border-radius:6px;background:var(--bg);cursor:pointer">↻ Cek Bayar</button>
                    @endif
                  </div>
                </td>
              @else
                <td data-l="Status">
                  <span class="status {{ $statusClass }}">{{ $order->status }}</span>
                  <div style="margin-top:.45rem">
                    <span class="status {{ $payInfo[1] }}" style="font-size:.66rem">{{ $payInfo[0] }}</span>
                  </div>
                  @if ($order->payment_status === 'pending')
                    <button type="button" class="btn-cek-status" data-order="{{ $order->order_id }}"
                            style="margin-top:.5rem;font-family:var(--font-mono);font-size:.68rem;padding:.3rem .6rem;border:1.5px solid var(--ink);border-radius:6px;background:var(--bg);cursor:pointer">↻ Cek Status Bayar</button>
                  @endif
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if (method_exists($orders, 'hasPages') && $orders->hasPages())
      <div style="margin-top:1.2rem;display:flex;justify-content:center">{{ $orders->links() }}</div>
    @endif
  @endif

</div>
@endsection

@push('scripts')
<script>
/* Tombol "Cek Status Bayar" -> tanya Status API Midtrans (tanpa webhook) -> reload */
document.querySelectorAll('.btn-cek-status').forEach((btn) => {
  btn.addEventListener('click', async () => {
    const orig = btn.textContent;
    btn.disabled = true;
    btn.textContent = '⏳ Mengecek...';
    try {
      const res = await fetch('/midtrans/sync-status/' + encodeURIComponent(btn.dataset.order), {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
      });
      await res.json();
      location.reload();
    } catch (e) {
      btn.disabled = false;
      btn.textContent = orig;
      alert('Gagal mengecek status. Coba lagi.');
    }
  });
});
</script>
@endpush
