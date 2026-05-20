<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed admin user
        User::firstOrCreate(
            ['email' => 'admin@sijaring.id'],
            [
                'name'      => 'Admin Si Jaring',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'phone'     => '+6281234567890',
                'is_active' => true,
            ]
        );

        // Seed demo pelanggan
        User::firstOrCreate(
            ['email' => 'budi@example.com'],
            [
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('budi123'),
                'role'      => 'pelanggan',
                'phone'     => '+6282111222333',
                'is_active' => true,
            ]
        );

        // Seed contact settings (used by /kontak + footer)
        $contacts = [
            'whatsapp'       => '+62 823 2927 7901',
            'whatsapp_link'  => '6282329277901',
            'email'          => 'cs@sijaring.id',
            'address'        => "Jl. Panjaitan Blok F, Gg. Sebelah Alfamart No. 108,\nRW.26 Lingk. Sadengan, Kebonsari,\nKec. Sumbersari, Kab. Jember, Jawa Timur 68122",
            'hours_weekday'  => '08.00 – 18.00',
            'hours_saturday' => '09.00 – 15.00',
            'hours_sunday'   => 'Tutup',
            'maps_embed_url' => 'https://www.google.com/maps?q=Jl.+Panjaitan+Sumbersari+Jember&output=embed',
            'maps_link'      => 'https://www.google.com/maps/search/?api=1&query=Jl.+Panjaitan+Sumbersari+Jember',
        ];
        foreach ($contacts as $key => $value) {
            ContactSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->call([
            KategoriSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}
