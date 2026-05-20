<?php

namespace App\Http\Controllers;

use App\Models\ContactSetting;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function show()
    {
        $cs = ContactSetting::all()->pluck('value', 'key');
        return view('kontak', compact('cs'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:30',
            'email'   => 'required|email',
            'topic'   => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $waLink = ContactSetting::get('whatsapp_link', '6282329277901');
        $waText = urlencode(
            "Halo Si Jaring 👋\n\n" .
            "Saya *{$data['name']}* ({$data['email']} / {$data['phone']}) ingin bertanya:\n" .
            "*Topik:* {$data['topic']}\n\n" .
            $data['message'] . "\n\n— Dikirim dari halaman Kontak Si Jaring"
        );

        session()->flash('success', '// Terima kasih! Pesan kamu sudah masuk. Kami balas paling lambat 1×24 jam.');

        return redirect()->route('kontak')->with('wa_url', "https://wa.me/{$waLink}?text={$waText}");
    }
}
