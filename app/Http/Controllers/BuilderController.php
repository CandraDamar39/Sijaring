<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuilderController extends Controller
{
    public function index()
    {
        return view('builder', [
            'show_result'     => false,
            'recommendations' => [],
            'summary'         => '',
            'old'             => ['jumlah' => 'kecil', 'skala' => 'ruangan', 'tipe' => 'kabel'],
        ]);
    }

    /**
     * POST /builder: validate, save inquiry, pick products per category,
     * stash them in session('builder_cart'), redirect to /keranjang.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jumlah' => 'required|in:kecil,menengah,besar,raksasa',
            'skala'  => 'required|in:ruangan,gedung,kampus,kota',
            'tipe'   => 'required|in:kabel,wireless,hybrid',
        ]);

        Inquiry::create([
            'user_id' => Auth::id(),
            'jumlah'  => $data['jumlah'],
            'skala'   => $data['skala'],
            'tipe'    => $data['tipe'],
        ]);

        $jumlahMap = [
            'kecil'    => ['Switch', 'Access Point', 'Kabel'],
            'menengah' => ['Switch', 'Router', 'Access Point', 'Kabel'],
            'besar'    => ['Switch', 'Router', 'Access Point', 'Kabel', 'Server'],
            'raksasa'  => ['Switch', 'Router', 'Access Point', 'Kabel', 'Server'],
        ];
        $skalaMap = [
            'ruangan' => ['Access Point', 'Switch', 'Kabel'],
            'gedung'  => ['Switch', 'Access Point', 'Router', 'Kabel'],
            'kampus'  => ['Router', 'Switch', 'Access Point', 'Kabel', 'Server'],
            'kota'    => ['Router', 'Server', 'Switch', 'Kabel'],
        ];

        $kategoriList = array_values(array_unique(array_merge(
            $jumlahMap[$data['jumlah']] ?? [],
            $skalaMap[$data['skala']] ?? []
        )));

        $cartItems = [];
        foreach ($kategoriList as $kategori) {
            $produk = Produk::aktif()
                ->where('kategori', $kategori)
                ->orderBy('stok', 'desc')
                ->first();
            if ($produk) {
                $cartItems[] = [
                    'id'    => $produk->kode_produk,
                    'name'  => $produk->nama,
                    'spec'  => $produk->spec,
                    'price' => (int) $produk->harga,
                    'qty'   => 1,
                ];
            }
        }

        session(['builder_cart' => $cartItems]);

        $count = count($cartItems);
        session()->flash('success', "// Rekomendasi siap! {$count} produk telah ditambahkan ke keranjang.");

        return redirect()->route('keranjang');
    }

    /** Background JSON endpoint kept for API compatibility */
    public function inquiry(Request $request)
    {
        $data = $request->validate([
            'jumlah' => 'required|in:kecil,menengah,besar,raksasa',
            'skala'  => 'required|in:ruangan,gedung,kampus,kota',
            'tipe'   => 'required|in:kabel,wireless,hybrid',
        ]);
        Inquiry::create([
            'user_id' => Auth::id(),
            'jumlah'  => $data['jumlah'],
            'skala'   => $data['skala'],
            'tipe'    => $data['tipe'],
        ]);
        return response()->json(['success' => true]);
    }
}
