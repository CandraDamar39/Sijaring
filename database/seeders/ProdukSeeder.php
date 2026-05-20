<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produks = [
            ['kode_produk'=>'SW-L3-24',     'nama'=>'Manageable Switch 24-Port L3',         'spec'=>'Gigabit · 4× SFP+ · VLAN · QoS',          'deskripsi'=>'Switch managed Layer-3 dengan 24 port Gigabit + 4 SFP+, VLAN, QoS, dan ACL. Cocok untuk core network kantor menengah-besar.', 'kategori'=>'Switch',       'harga'=>6450000,  'stok'=>5,  'is_aktif'=>true,  'tone'=>'tone-pink',   'foto'=>'https://images.unsplash.com/photo-1750711731797-25c3f2551ff8?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['enterprise','data-center']],
            ['kode_produk'=>'RT-AX3000',    'nama'=>'Wireless Router Dual-Band AX3000',      'spec'=>'Wi-Fi 6 · 3× antena eksternal · mesh',    'deskripsi'=>'Router Wi-Fi 6 dengan dukungan mesh, throughput 2402+574 Mbps, 3 antena eksternal. Cocok untuk UKM dan cabang.',          'kategori'=>'Router',       'harga'=>1850000,  'stok'=>12, 'is_aktif'=>true,  'tone'=>'tone-cream',  'foto'=>'https://plus.unsplash.com/premium_photo-1671029898272-5dc5267f9c13?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['ukm','sekolah']],
            ['kode_produk'=>'UTP-CAT6-305', 'nama'=>'Kabel UTP Cat6 305m Box',               'spec'=>'23AWG · pure copper · CMR',                'deskripsi'=>'Kabel UTP Cat6 23AWG pure copper standar CMR, panjang 305 meter per box. Cocok untuk instalasi gedung & kabling permanen.', 'kategori'=>'Kabel',        'harga'=>1240000,  'stok'=>20, 'is_aktif'=>true,  'tone'=>'tone-yellow', 'foto'=>'https://images.unsplash.com/photo-1546124404-9e7e3cac2ec1?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['enterprise','isp','sekolah']],
            ['kode_produk'=>'AP-OUT-5G',    'nama'=>'Access Point Outdoor 5GHz',             'spec'=>'PtMP · IP67 · PoE 24V · 15km range',       'deskripsi'=>'Access point outdoor IP67 dengan dukungan PtMP, PoE 24V, dan beamforming untuk koneksi long-range hingga 15 km.',         'kategori'=>'Access Point', 'harga'=>2150000,  'stok'=>8,  'is_aktif'=>true,  'tone'=>'tone-cyan',   'foto'=>'https://images.unsplash.com/photo-1663932210347-164a05ed0ccd?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['isp','enterprise']],
            ['kode_produk'=>'SRV-RACK-1U',  'nama'=>'Rack Server 1U Intel Xeon',             'spec'=>'Xeon Silver · 32GB ECC · dual PSU · IPMI', 'deskripsi'=>'Server rack-mount 1U dengan Xeon Silver, 32GB ECC RAM, dual PSU hot-swap, IPMI. Cocok untuk virtualization & web hosting.', 'kategori'=>'Server',       'harga'=>18500000, 'stok'=>3,  'is_aktif'=>true,  'tone'=>'tone-ink',    'foto'=>'https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['enterprise','data-center']],
            ['kode_produk'=>'SW-L2-08',     'nama'=>'Unmanaged Switch 8-Port Gigabit',       'spec'=>'Plug-and-play · auto MDI/MDI-X · desktop','deskripsi'=>'Switch plug-and-play 8 port Gigabit dengan auto MDI/MDI-X. Cocok untuk ruang kerja kecil atau lab.',                       'kategori'=>'Switch',       'harga'=>425000,   'stok'=>50, 'is_aktif'=>true,  'tone'=>'tone-cream',  'foto'=>'https://images.unsplash.com/photo-1611174743420-3d7df880ce32?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['ukm','sekolah']],
            ['kode_produk'=>'RT-CCR-1036',  'nama'=>'Core Router Mikrotik CCR1036',          'spec'=>'36-core · 4GB RAM · 16× GbE · 2× SFP+',   'deskripsi'=>'Cloud Core Router 36-core dengan throughput hingga 28 Gbps. Untuk ISP & data center kelas menengah.',                     'kategori'=>'Router',       'harga'=>14750000, 'stok'=>4,  'is_aktif'=>true,  'tone'=>'tone-pink',   'foto'=>'https://images.unsplash.com/photo-1591808216268-ce0b82787efe?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['isp','data-center']],
            ['kode_produk'=>'AP-CEIL-AX',   'nama'=>'Ceiling Mount AP Wi-Fi 6',              'spec'=>'AX3000 · PoE 802.3af · 500+ users',        'deskripsi'=>'Access point ceiling Wi-Fi 6 dengan 2.4+5 GHz, MU-MIMO, throughput 3 Gbps per radio. Mendukung controller terpusat.',     'kategori'=>'Access Point', 'harga'=>3250000,  'stok'=>15, 'is_aktif'=>true,  'tone'=>'tone-cyan',   'foto'=>'https://images.unsplash.com/photo-1614624532983-4ce03382d63d?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['enterprise','sekolah']],
            ['kode_produk'=>'UTP-CAT5E-100','nama'=>'Kabel UTP Cat5e 100m',                  'spec'=>'24AWG · pure copper · indoor',             'deskripsi'=>'Kabel UTP Cat5e ekonomis 100 meter per box. Untuk instalasi temporer atau jaringan rumahan.',                              'kategori'=>'Kabel',        'harga'=>285000,   'stok'=>35, 'is_aktif'=>true,  'tone'=>'tone-yellow', 'foto'=>'https://images.unsplash.com/photo-1605647540924-852290f6b0d5?w=720&q=70&auto=format&fit=crop', 'kategori_rel'=>['ukm']],
            ['kode_produk'=>'SW-LEGACY-16', 'nama'=>'[DISCONTINUED] Switch 16-Port Cisco',  'spec'=>'52-port · old stock',                      'deskripsi'=>'Produk discontinued — disimpan untuk testing scopeAktif(). Tidak tampil di katalog publik.',                                'kategori'=>'Switch',       'harga'=>1850000,  'stok'=>0,  'is_aktif'=>false, 'tone'=>'tone-cream',  'foto'=>null,                                                                                                  'kategori_rel'=>[]],
        ];

        foreach ($produks as $p) {
            $relSlugs = $p['kategori_rel'];
            unset($p['kategori_rel']);

            $produk = Produk::updateOrCreate(['kode_produk' => $p['kode_produk']], $p);

            if (!empty($relSlugs)) {
                $kategoriIds = Kategori::whereIn('slug', $relSlugs)->pluck('id');
                $produk->kategoris()->sync($kategoriIds);
            }
        }
    }
}
