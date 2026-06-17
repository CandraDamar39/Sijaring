<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'pay'          => 'nullable|in:bca,mandiri,bni,qris,cod',
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
                'payment_method' => $data['pay'] ?? 'bca',
                'subtotal'       => $subtotal,
                'shipping'       => $shipping,
                'total'          => $total,
                'payment_status' => 'pending',
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

        // ── Susun item_details Midtrans (harga integer; sertakan ongkir) ──
        $itemDetails = [];
        foreach ($data['items'] as $i) {
            $itemDetails[] = [
                'id'       => (string) $i['id'],
                'price'    => (int) round($i['price']),
                'quantity' => (int) $i['qty'],
                'name'     => mb_substr($i['name'], 0, 50),
            ];
        }
        $itemDetails[] = [
            'id' => 'SHIPPING', 'price' => (int) round($shipping), 'quantity' => 1, 'name' => 'Ongkos Kirim',
        ];
        // gross_amount WAJIB sama dengan jumlah item_details.
        $gross = collect($itemDetails)->sum(fn ($d) => $d['price'] * $d['quantity']);

        // ── Minta Snap token ke Midtrans (opsional & aman bila key belum diisi) ──
        $snapToken = $this->requestSnapToken($order, $orderId, $gross, $itemDetails, $data);

        $waMsg = sprintf(
            'Halo SiJaring, saya baru order %s senilai Rp %s. Mohon konfirmasinya.',
            $orderId,
            number_format($total, 0, ',', '.')
        );

        return response()->json([
            'success'       => true,
            'order_id'      => $orderId,
            'total'         => $total,
            'wa_link'       => 'https://wa.me/6282329277901?text=' . urlencode($waMsg),
            'snap_token'    => $snapToken,
            'client_key'    => config('services.midtrans.client_key'),
            'is_production' => $this->isProduction(),
        ]);
    }

    /**
     * Memanggil Snap API Midtrans untuk membuat transaksi & mendapatkan token.
     * Mengembalikan null bila key belum dikonfigurasi atau terjadi error —
     * sehingga order tetap tersimpan dan checkout tidak pernah gagal total.
     */
    private function requestSnapToken(Order $order, string $orderId, int $gross, array $itemDetails, array $data): ?string
    {
        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return null; // Midtrans belum disetel → pakai alur lama (WhatsApp).
        }

        $baseUrl = $this->isProduction()
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        try {
            $resp = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(15)
                ->post($baseUrl . '/snap/v1/transactions', [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => $gross,
                    ],
                    'item_details'     => $itemDetails,
                    'customer_details' => [
                        'first_name' => $data['name'],
                        'email'      => $data['email'],
                        'phone'      => $data['phone'],
                    ],
                ]);

            if ($resp->successful() && $resp->json('token')) {
                $token = $resp->json('token');
                $order->update(['snap_token' => $token]);

                return $token;
            }

            Log::warning('Midtrans Snap gagal', ['status' => $resp->status(), 'body' => $resp->body()]);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap error: ' . $e->getMessage());
        }

        return null;
    }

    private function isProduction(): bool
    {
        return filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
    }
}
