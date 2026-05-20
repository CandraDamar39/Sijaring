<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $isAdmin = Auth::user()->isAdmin();

        if ($isAdmin) {
            $orders = Order::with(['items', 'user'])
                ->latest()
                ->paginate(20);
        } else {
            $orders = Order::where('user_id', Auth::id())
                ->with('items')
                ->latest()
                ->get();
        }

        return view('riwayat', compact('orders', 'isAdmin'));
    }
}
