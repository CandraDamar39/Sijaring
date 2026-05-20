<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        // ── FEATURE 4: Session Visit Counter ──────────────────────
        $now    = now();
        $visits = $request->session()->get('katalog_visits', 0) + 1;
        $first  = $request->session()->get('katalog_first_visit', $now->toDateTimeString());
        $last   = $now->toDateTimeString();

        $request->session()->put('katalog_visits', $visits);
        $request->session()->put('katalog_first_visit', $first);
        $request->session()->put('katalog_last_visit', $last);
        // ──────────────────────────────────────────────────────────

        // Filter awal via query string (render server-side; AJAX mengambil alih setelahnya)
        $search   = trim($request->get('q', ''));
        $kategori = $request->get('kategori', '');

        $query = Produk::aktif();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('spec', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        $produk      = $query->orderBy('id')->get();
        $kategoris   = Produk::aktif()->distinct()->pluck('kategori')->sort()->values();
        $totalProduk = Produk::aktif()->count();

        return view('katalog', compact(
            'produk', 'kategoris', 'totalProduk', 'search', 'kategori',
            'visits', 'first', 'last'
        ));
    }

    /**
     * FEATURE 2: Live search produk via AJAX (GET). Mengembalikan JSON,
     * tanpa reload halaman. Mencari nama, spec, kode_produk, deskripsi.
     */
    public function ajaxSearch(Request $request)
    {
        $q        = $request->input('q', '');
        $kategori = $request->input('kategori', 'all');

        $query = Produk::aktif();

        if ($q) {
            $query->where(function ($builder) use ($q) {
                $builder->where('nama', 'like', "%{$q}%")
                        ->orWhere('spec', 'like', "%{$q}%")
                        ->orWhere('kode_produk', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($kategori && $kategori !== 'all') {
            $query->where('kategori', $kategori);
        }

        $produk = $query->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'count'   => $produk->count(),
            'produk'  => $produk->map(fn ($p) => [
                'id'        => $p->id,
                'kode'      => $p->kode_produk,
                'nama'      => $p->nama,
                'spec'      => $p->spec,
                'kategori'  => $p->kategori,
                'harga'     => (int) $p->harga,
                'harga_fmt' => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                'stok'      => $p->stok,
                'foto'      => $p->foto,
                'tone'      => $p->tone ?? 'tone-cream',
            ]),
        ]);
    }

    /**
     * FEATURE 4: Reset hitungan kunjungan dari session.
     */
    public function resetVisits(Request $request)
    {
        $request->session()->forget([
            'katalog_visits',
            'katalog_first_visit',
            'katalog_last_visit',
        ]);

        session()->flash('success', 'Hitungan kunjungan berhasil direset.');

        return redirect()->route('katalog');
    }
}
