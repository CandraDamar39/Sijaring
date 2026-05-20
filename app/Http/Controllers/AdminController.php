<?php

namespace App\Http\Controllers;

use App\Models\ContactSetting;
use App\Models\Kategori;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_produk'   => Produk::count(),
            'produk_aktif'   => Produk::aktif()->count(),
            'total_user'     => User::where('role', 'pelanggan')->count(),
            'total_kategori' => Kategori::count(),
        ];

        $produk = Produk::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'produk'));
    }

    public function users()
    {
        $users = User::where('role', 'pelanggan')->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function produk()
    {
        $produk = Produk::latest()->paginate(15);
        return view('admin.produk', compact('produk'));
    }

    public function storeProduk(Request $request)
    {
        $data = $this->validateProduk($request, true);
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? 1);
        Produk::create($data);
        session()->flash('success', 'Produk "' . $data['nama'] . '" berhasil ditambahkan.');
        return redirect()->route('admin.produk');
    }

    public function updateProduk(Request $request, Produk $produk)
    {
        $data = $this->validateProduk($request, false);
        $data['is_aktif'] = (bool) ($data['is_aktif'] ?? 0);
        // Don't allow kode_produk change on edit
        unset($data['kode_produk']);
        $produk->update($data);
        session()->flash('success', 'Produk "' . $produk->nama . '" berhasil diperbarui.');
        return redirect()->route('admin.produk');
    }

    public function destroyProduk(Produk $produk)
    {
        $produk->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
        return back();
    }

    public function laporan()
    {
        // Pesanan valid = bukan Dibatalkan
        $valid = Order::where('status', '!=', 'Dibatalkan');

        $totalRevenue = (clone $valid)->sum('total');
        $totalValid   = (clone $valid)->count();
        $totalOrders  = Order::count();
        $avgOrder     = $totalValid > 0 ? (int) round($totalRevenue / $totalValid) : 0;
        $unitsSold    = (int) OrderItem::whereHas('order', function ($q) {
            $q->where('status', '!=', 'Dibatalkan');
        })->sum('qty');

        // Pesanan per status (untuk chart)
        $statuses = ['Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
        $byStatus = collect($statuses)->mapWithKeys(fn ($s) => [
            $s => Order::where('status', $s)->count(),
        ]);
        $maxStatus = max(1, $byStatus->max());

        // Produk terlaris (top 5 by qty)
        $topProducts = OrderItem::select(
                'produk_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(harga * qty) as total_rev')
            )
            ->groupBy('produk_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
        $maxQty = max(1, (int) $topProducts->max('total_qty'));

        // Pendapatan 6 bulan terakhir
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $rev = Order::where('status', '!=', 'Dibatalkan')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');
            $months->push([
                'label' => $month->translatedFormat('M Y'),
                'value' => (int) $rev,
            ]);
        }
        $maxMonth = max(1, (int) $months->max('value'));

        return view('admin.laporan', compact(
            'totalRevenue', 'totalValid', 'totalOrders', 'avgOrder', 'unitsSold',
            'byStatus', 'maxStatus', 'topProducts', 'maxQty', 'months', 'maxMonth'
        ));
    }

    public function contactSettings()
    {
        $settings = ContactSetting::all()->pluck('value', 'key');
        return view('admin.contact-settings', compact('settings'));
    }

    public function updateContactSettings(Request $request)
    {
        $data = $request->validate([
            'whatsapp'       => 'required|string|max:30',
            'whatsapp_link'  => 'required|string|max:20',
            'email'          => 'required|email|max:100',
            'address'        => 'required|string|max:500',
            'hours_weekday'  => 'required|string|max:50',
            'hours_saturday' => 'required|string|max:50',
            'hours_sunday'   => 'required|string|max:50',
            'maps_embed_url' => 'nullable|url|max:500',
            'maps_link'      => 'nullable|url|max:500',
        ]);
        foreach ($data as $key => $value) {
            // Optional URL fields may arrive as null when blank — coerce to empty string
            ContactSetting::updateOrCreate(['key' => $key], ['value' => (string) ($value ?? '')]);
        }
        session()->flash('success', 'Info kontak berhasil diperbarui.');
        return back();
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Menunggu Konfirmasi,Diproses,Dikirim,Selesai,Dibatalkan',
        ]);
        $order->update(['status' => $request->status]);
        session()->flash('success', "Status pesanan {$order->order_id} diperbarui.");
        return back();
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin()) {
            session()->flash('error', 'Tidak bisa menghapus akun admin.');
            return back();
        }
        $name = $user->name;
        $user->delete();
        session()->flash('success', "Akun {$name} berhasil dihapus.");
        return back();
    }

    private function validateProduk(Request $request, bool $isCreate): array
    {
        $rules = [
            'nama'      => 'required|string|max:255',
            'spec'      => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori'  => 'required|in:Switch,Router,Access Point,Kabel,Server',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'foto'      => 'nullable|url',
            'tone'      => 'required|in:tone-pink,tone-cyan,tone-yellow,tone-cream,tone-green,tone-ink',
            'is_aktif'  => 'nullable|in:0,1',
        ];
        if ($isCreate) {
            $rules['kode_produk'] = 'required|string|max:50|unique:produks,kode_produk';
        }
        return $request->validate($rules);
    }
}
