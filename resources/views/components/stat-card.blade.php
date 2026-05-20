@props([
    'judul' => '',
    'nilai' => '',
    'warna' => '',   // '', 'pink', 'cyan', 'yellow'
])

<div class="stat-card {{ $warna }}">
    <div class="lbl">{{ $judul }}</div>
    <div class="num">{{ $nilai }}</div>
</div>
