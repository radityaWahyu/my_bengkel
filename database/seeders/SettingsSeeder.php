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
            ],
            [
                'name' => 'logo bengkel',
                'data' => '',
            ],
            [
                'name' => 'alamat bengkel',
                'data' => 'Gedung TKR SMKN 1 Purwoari Jl. Raya Purwosari No 1 Kabupaten Pasuruan',
            ],
            [
                'name' => 'nama kontak',
                'data' => 'Andri Purwantono',
            ],
            [
                'name' => 'telepon kontak',
                'data' => '234234234',
            ],
            [
                'name' => 'Whatsapp kontak',
                'data' => '0920394203',
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
