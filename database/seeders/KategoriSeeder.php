<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Enterprise',   'slug' => 'enterprise',   'deskripsi' => 'Perangkat untuk kebutuhan skala perusahaan besar'],
            ['nama' => 'UKM',          'slug' => 'ukm',          'deskripsi' => 'Perangkat ringkas untuk usaha kecil & menengah'],
            ['nama' => 'Data Center',  'slug' => 'data-center',  'deskripsi' => 'Perangkat high-density untuk pusat data'],
            ['nama' => 'ISP',          'slug' => 'isp',          'deskripsi' => 'Khusus untuk Internet Service Provider'],
            ['nama' => 'Sekolah',      'slug' => 'sekolah',      'deskripsi' => 'Perangkat ramah anggaran untuk institusi pendidikan'],
        ];

        foreach ($kategoris as $k) {
            Kategori::updateOrCreate(['slug' => $k['slug']], $k);
        }
    }
}
