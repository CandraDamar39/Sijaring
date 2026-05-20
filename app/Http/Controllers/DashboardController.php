<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStock      = Produk::aktif()->sum('stok');
        $totalCategories = Produk::aktif()->distinct('kategori')->count('kategori');
        $totalOrders     = Order::count();

        return view('dashboard', compact('totalStock', 'totalCategories', 'totalOrders'));
    }
}
