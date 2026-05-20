<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'phone'        => 'required|string|max:30',
            'company'      => 'nullable|string|max:255',
            'address'      => 'required|string',
            'city'         => 'required|string|max:100',
            'zip'          => 'required|string|max:10',
            'pay'          => 'required|in:bca,mandiri,bni,qris,cod',
            'items'        => 'required|array|min:1',
            'items.*.id'   => 'required',
            'items.*.name' => 'required|string',
            'items.*.price'=> 'required|numeric|min:0',
            'items.*.qty'  => 'required|integer|min:1',
        ]);

        $shipping = 25000;
        $subtotal = collect($data['items'])->sum(fn ($i) => (float) $i['price'] * (int) $i['qty']);
        $total    = $subtotal + $shipping;
        $orderId  = 'SJ-' . substr((string) time(), -6) . '-' . random_int(100, 999);

        $order = DB::transaction(function () use ($data, $subtotal, $shipping, $total, $orderId) {
            $order = Order::create([
                'order_id'       => $orderId,
                'user_id'        => Auth::id(),
                'name'           => $data['name'],
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'company'        => $data['company'] ?? null,
                'address'        => $data['address'],
                'city'           => $data['city'],
                'zip'            => $data['zip'],
                'payment_method' => $data['pay'],
                'subtotal'       => $subtotal,
                'shipping'       => $shipping,
                'total'          => $total,
            ]);

            foreach ($data['items'] as $item) {
                $produk = Produk::where('kode_produk', $item['id'])->first();
                $order->items()->create([
                    'produk_id'   => $produk?->id,
                    'produk_name' => $item['name'],
                    'produk_spec' => $item['spec'] ?? null,
                    'harga'       => $item['price'],
                    'qty'         => $item['qty'],
                ]);
            }

            return $order;
        });

        $waMsg = sprintf(
            'Halo SiJaring, saya baru order %s senilai Rp %s. Mohon konfirmasinya.',
            $orderId,
            number_format($total, 0, ',', '.')
        );

        return response()->json([
            'success'  => true,
            'order_id' => $orderId,
            'total'    => $total,
            'wa_link'  => 'https://wa.me/6282329277901?text=' . urlencode($waMsg),
        ]);
    }
}
