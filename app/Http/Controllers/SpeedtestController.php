<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * FEATURE 1: Speed Test Jaringan.
 *
 * Tiga endpoint ringan yang dipanggil front-end secara asinkronus (Fetch API)
 * untuk mengukur kualitas koneksi pengguna terhadap server aplikasi:
 *   - ping()     : balasan kecil & cepat  -> klien menghitung latency (round-trip).
 *   - download() : mengalirkan N byte acak -> klien menghitung kecepatan download.
 *   - upload()   : menerima N byte         -> klien menghitung kecepatan upload.
 *
 * Relevan dengan bisnis Si Jaring (distributor alat jaringan): calon pelanggan
 * bisa langsung menguji koneksinya sebelum berkonsultasi soal infrastruktur.
 */
class SpeedtestController extends Controller
{
    /** Probe latensi: balasan instan agar klien bisa mengukur round-trip time. */
    public function ping(Request $request)
    {
        return response()
            ->json(['pong' => true, 't' => (int) round(microtime(true) * 1000)])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /** Alirkan N byte acak (incompressible) untuk mengukur kecepatan download. */
    public function download(Request $request): StreamedResponse
    {
        // Default 10 MB; dibatasi 0–50 MB agar aman.
        $bytes = (int) $request->query('bytes', 10_000_000);
        $bytes = max(0, min($bytes, 50_000_000));

        return response()->stream(function () use ($bytes) {
            $chunkSize = 65536; // 64 KB per potongan
            $remaining = $bytes;

            while ($remaining > 0) {
                $write = (int) min($chunkSize, $remaining);
                // random_bytes => tidak bisa dikompres gzip, hasil ukur lebih akurat.
                echo random_bytes($write);
                $remaining -= $write;

                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            }
        }, 200, [
            'Content-Type'      => 'application/octet-stream',
            'Content-Length'    => (string) $bytes,
            'Content-Encoding'  => 'identity',
            'Cache-Control'     => 'no-store, no-cache, must-revalidate, max-age=0',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /** Terima unggahan biner, balas jumlah byte diterima (untuk hitung upload). */
    public function upload(Request $request)
    {
        $size = strlen($request->getContent());

        return response()
            ->json(['received' => $size])
            ->header('Cache-Control', 'no-store');
    }
}
