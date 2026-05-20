<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function show()
    {
        return view('profil', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:30',
            'company' => 'nullable|string|max:255',
            'bio'     => 'nullable|string|max:1000',
        ]);
        Auth::user()->update($data);
        session()->flash('success', '// Data pribadi berhasil disimpan.');
        return back()->with('active_tab', 'profile');
    }

    public function updateAddress(Request $request)
    {
        $data = $request->validate([
            'address'       => 'required|string',
            'city'          => 'required|string|max:100',
            'province'      => 'required|string|max:100',
            'zip'           => 'required|string|max:10',
            'address_label' => 'nullable|string|max:50',
        ]);
        Auth::user()->update($data);
        session()->flash('success', '// Alamat pengiriman berhasil disimpan.');
        return back()->with('active_tab', 'address');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old'  => 'required|string',
            'new'  => 'required|string|min:6',
            'conf' => 'required|same:new',
        ]);

        if (!Hash::check($request->old, Auth::user()->password)) {
            return back()
                ->withErrors(['old' => 'Kata sandi lama tidak cocok.'])
                ->with('active_tab', 'security');
        }

        Auth::user()->update(['password' => Hash::make($request->new)]);
        session()->flash('success', '// Kata sandi berhasil diubah.');
        return back()->with('active_tab', 'security');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return back()->with('error', 'Akun admin tidak bisa dihapus dari halaman profil.');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();
        return redirect()->route('home')->with('success', 'Akun berhasil dihapus.');
    }
}
