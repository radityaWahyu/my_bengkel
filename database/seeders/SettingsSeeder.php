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
                'name' => 'Instagram',
                'data' => 'http://instagram.com/smkn1purwsoari',
            ],
            [
                'name' => 'Facebook',
                'data' => 'http://facebook.com/smkn1purwsoari',
            ],
            [
                'name' => 'Youtube',
                'data' => 'http://youtube.com/smkn1purwsoari',
            ],
            [
                'name' => 'Alamat',
                'data' => 'SMKN 1 Purwosari Jl. Raya Purwosari no 1',
            ],
            [
                'name' => 'Google Map',
                'data' => '<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15812.83393701064!2d112.7483609!3d-7.7677029!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7d3c5631acbf5%3A0xa71d9f205034b481!2sSMK%20Negeri%201%20Purwosari!5e0!3m2!1sen!2sid!4v1724132742958!5m2!1sen!2sid" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
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
