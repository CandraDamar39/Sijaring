<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreferensiController extends Controller
{
    /**
     * Menampilkan halaman preferensi. Nilai awal dibaca dari cookie
     * agar form menampilkan pilihan terakhir pengguna.
     */
    public function show(Request $request)
    {
        $theme    = $request->cookie('sj_theme', 'light');
        $fontSize = $request->cookie('sj_fontsize', 'normal');

        return view('preferensi', compact('theme', 'fontSize'));
    }

    /**
     * Menyimpan preferensi: validasi, balas JSON, dan lampirkan cookie
     * (berlaku 1 tahun) lewat ->cookie() pada response.
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'theme'    => 'required|in:light,dark,system',
            'fontsize' => 'required|in:small,normal,large',
        ]);

        return response()
            ->json([
                'success'  => true,
                'message'  => 'Preferensi berhasil disimpan.',
                'theme'    => $data['theme'],
                'fontsize' => $data['fontsize'],
            ])
            ->cookie('sj_theme', $data['theme'], 60 * 24 * 365)
            ->cookie('sj_fontsize', $data['fontsize'], 60 * 24 * 365);
    }
}
