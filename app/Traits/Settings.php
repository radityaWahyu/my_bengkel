<?php

namespace App\Traits;


use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait Settings
{

    public function getSetting(): array
    {
        $settingData = [];
        $settings = Setting::query()->get();
        foreach ($settings as $setting) {
            $name = Str::replace(' ', '_', Str::lower($setting->name));
            if ($setting['type'] == 'image') {
                $settingData += [$name => "data:image/jpeg;base64," . base64_encode(Storage::get($setting->data))];
            } else {
                $settingData += [$name => $setting->data];
            }
        }

        return $settingData;
    }
}
