<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'nama bengkel',
                'data' => 'Bengkel TKR SMKN 1 Purwosari',
                'type' => 'text',
                'description' => 'Nama bengkel dipergunakan untuk mensetting nama bengkel dari setiap invoice yang di cetak oleh sistem.'
            ],
            [
                'name' => 'logo bengkel',
                'data' => 'images/logo.png',
                'type' => 'image',
                'description' => 'Logo bengkel merupakan sebuah gambar logo dari bengkel dan akan dimasukkan kedalam setiap laporan atau invoice'
            ],
            [
                'name' => 'alamat bengkel',
                'data' => 'Gedung TKR SMKN 1 Purwoari Jl. Raya Purwosari No 1 Kabupaten Pasuruan',
                'type' => 'textarea',
                'description' => 'Alamat bengkel merupakan alamat lengkap sebagai alamat kontak dari bengkel'
            ],
            [
                'name' => 'nama kontak',
                'data' => 'Andri Purwantono',
                'type' => 'text',
                'description' => 'Kontak dipergunakan sebagai penanggung jawa dari bengkel.'
            ],
            [
                'name' => 'telepon kontak',
                'data' => '234234234',
                'type' => 'text',
                'description' => 'Telepon kontak merupakan no telepon dari penanggung jawab bengkel yang menggunakan sistem ini.'
            ],
            [
                'name' => 'Whatsapp kontak',
                'data' => '0920394203',
                'type' => 'text',
                'description' => 'Whatsapp kontak merupakan no whatsapp dari penanggung jawab bengkel yang menggunakan sistem ini.'
            ],

        ];

        print("Mulai input data pengaturan \n");
        foreach ($settings as $setting) {
            print("Input data" . $setting['name'] . "... \n");
            Setting::create($setting);
        }
        print_r("Input data pengaturan selesai");
    }
}
