<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait Whatsapp
{

    public function getWhatsapp($whatsapp)
    {
        //Terlebih dahulu kita trim dl
        $whatsapp = trim($whatsapp);
        //bersihkan dari karakter yang tidak perlu
        $whatsapp = strip_tags($whatsapp);
        // Berishkan dari spasi
        $whatsapp = str_replace(" ", "", $whatsapp);
        // bersihkan dari bentuk seperti  (022) 66677788
        $whatsapp = str_replace("(", "", $whatsapp);
        // bersihkan dari format yang ada titik seperti 0811.222.333.4
        $whatsapp = str_replace(".", "", $whatsapp);

        //cek apakah mengandung karakter + dan 0-9
        if (!preg_match('/[^+0-9]/', trim($whatsapp))) {
            // cek apakah no hp karakter 1-3 adalah +62
            if (substr(trim($whatsapp), 0, 3) == '+62') {
                $whatsapp = trim($whatsapp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif (substr($whatsapp, 0, 1) == '0') {
                $whatsapp = '+62' . substr($whatsapp, 1);
            }
        }
        return $whatsapp;
    }
}
