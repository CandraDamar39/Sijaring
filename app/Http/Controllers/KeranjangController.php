<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class KeranjangController extends Controller
{
    public function index()
    {
        $builderCart = session('builder_cart', []);
        $rekomendasi = collect();
        $totalHarga  = 0;

        foreach ($builderCart as $item) {
            $produk = Produk::where('kode_produk', $item['id'])->first();
            if ($produk) {
                $rekomendasi->push($produk);
                $totalHarga += (int) $produk->harga;
            }
        }

        return view('keranjang', compact('rekomendasi', 'builderCart', 'totalHarga'));
    }

    public function clear()
    {
        session()->forget('builder_cart');
        return redirect()->route('builder')->with('success', '// Keranjang dikosongkan.');
    }
}
