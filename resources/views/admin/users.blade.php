@extends('layouts.app')

@section('title', 'Admin · Pelanggan — Si Jaring Nusantara')

@section('content')
<div style="max-width:var(--container);margin:0 auto;padding:6rem 1.4rem 4rem;">

  <div class="page-head" style="margin-top:1rem">
    <div>
      <span class="kbd">⌘ ADMIN / PELANGGAN</span>
      <h1 style="margin-top:.5rem">Data Pelanggan</h1>
      <p class="lead">{{ $users->total() }} pelanggan terdaftar.</p>
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
    <a href="{{ route('admin.users') }}" class="active">Pelanggan</a>
  </div>

  <div class="table-wrap">
    <div class="table-head">
      <h3>Daftar Pelanggan</h3>
      <span class="kbd">{{ $users->total() }} entries</span>
    </div>
    <table class="t">
      <thead>
        <tr>
          <th>ID</th><th>Nama</th><th>Email</th><th>WhatsApp</th>
          <th>Status</th><th>Bergabung</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $u)
          <tr>
            <td class="oid" data-l="ID">#{{ str_pad($u->id, 4, '0', STR_PAD_LEFT) }}</td>
            <td data-l="Nama">{{ $u->name }}</td>
            <td data-l="Email"><span style="font-family:var(--font-mono);font-size:.85rem">{{ $u->email }}</span></td>
            <td data-l="WhatsApp">{{ $u->phone ?? '—' }}</td>
            <td data-l="Status">
              <span class="status {{ $u->is_active ? 'done' : 'cancel' }}">
                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td data-l="Bergabung" style="font-family:var(--font-mono);font-size:.78rem;color:var(--muted)">
              {{ $u->created_at->translatedFormat('d M Y') }}
            </td>
            <td data-l="Aksi">
              <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                    onsubmit="return confirm('Hapus akun {{ $u->name }}? Ini tidak bisa dibatalkan.')"
                    style="display:inline;margin:0">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger" style="padding:.35rem .7rem;font-size:.75rem">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;padding:2rem">Belum ada pelanggan terdaftar.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($users->hasPages())
    <div style="margin-top:1.2rem;display:flex;justify-content:center">{{ $users->links() }}</div>
  @endif

</div>
@endsection
