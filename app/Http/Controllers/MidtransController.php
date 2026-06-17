<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi status pembayaran Midtrans. Dua jalur:
 *  - notification() : webhook server-to-server (perlu URL publik / ngrok / deploy).
 *  - syncStatus()   : KITA yang bertanya ke Status API Midtrans (cocok untuk
 *                     demo localhost tanpa URL publik). Dipanggil otomatis saat
 *                     popup Snap sukses, atau manual lewat tombol "Cek Status".
 */
class MidtransController extends Controller
{
    /** Webhook dari server Midtrans (dikecualikan dari CSRF di bootstrap/app.php). */
    public function notification(Request $request)
    {
        $orderId      = $request->input('order_id');
        $statusCode   = $request->input('status_code', '');
        $grossAmount  = $request->input('gross_amount', '');
        $signatureKey = $request->input('signature_key', '');

        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey) || ! $orderId) {
            return response()->json(['message' => 'ignored'], 200);
        }

        // Verifikasi keaslian: sha512(order_id + status_code + gross_amount + server_key).
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (! hash_equals($expected, (string) $signatureKey)) {
            Log::warning('Midtrans signature mismatch', ['order_id' => $orderId]);

            return response()->json(['message' => 'invalid signature'], 403);
        }

        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return response()->json(['message' => 'order not found'], 404);
        }

        $this->applyStatus($order, $request->input('transaction_status', ''), $request->input('fraud_status'));

        return response()->json(['message' => 'ok'], 200);
    }

    /**
     * Tanya status transaksi langsung ke Status API Midtrans lalu perbarui order.
     * Tanpa webhook/ngrok — cocok untuk demo localhost.
     */
    public function syncStatus(Request $request, string $orderId)
    {
        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return response()->json(['success' => false, 'message' => 'Midtrans belum dikonfigurasi.'], 400);
        }

        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
        }

        $base = $this->isProduction() ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';

        try {
            $resp = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(15)
                ->get($base . '/v2/' . $orderId . '/status');
        } catch (\Throwable $e) {
            Log::error('Midtrans status error: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Gagal menghubungi Midtrans.'], 502);
        }

        // 404 dari Midtrans = transaksi belum ada (belum dibayar) -> biarkan apa adanya.
        if (! $resp->successful()) {
            return response()->json([
                'success'        => true,
                'payment_status' => $order->payment_status,
                'status'         => $order->status,
                'note'           => 'Transaksi belum tercatat di Midtrans (belum dibayar).',
            ]);
        }

        $this->applyStatus($order, $resp->json('transaction_status', ''), $resp->json('fraud_status'));

        return response()->json([
            'success'        => true,
            'payment_status' => $order->payment_status,
            'status'         => $order->status,
        ]);
    }

    /** Petakan transaction_status Midtrans -> payment_status internal, lalu simpan. */
    private function applyStatus(Order $order, string $transaction, ?string $fraud): void
    {
        $payment = match (true) {
            in_array($transaction, ['capture', 'settlement'], true) => $fraud === 'challenge' ? 'challenge' : 'paid',
            $transaction === 'pending'                              => 'pending',
            in_array($transaction, ['deny', 'cancel'], true)       => 'failed',
            $transaction === 'expire'                              => 'expired',
            default                                                 => $order->payment_status,
        };

        $order->payment_status = $payment;
        if ($payment === 'paid' && $order->status === 'Menunggu Konfirmasi') {
            $order->status = 'Diproses';
        }
        $order->save();
    }

    private function isProduction(): bool
    {
        return filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
    }
}
