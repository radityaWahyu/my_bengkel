<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::select('id', 'name', 'data', 'type', 'description')
            ->get();


        return inertia('Setting/Index', [
            'settings' => fn() => SettingResource::collection($settings),
        ]);
    }



    /**
     * store.
     */
    public function store(SettingRequest $request)
    {
        try {
            foreach ($request->settings as $setting) {
                if ($setting['id'] == 2) {
                    if ($request->hasFile("settings.1.value")) {

                        $request->settings[1]['value']->storeAs('images/', 'logo.jpg');

                        Setting::find($setting['id'])->update([
                            'data' => 'images/logo.jpg'
                        ]);
                    }
                } else {
                    Setting::find($setting['id'])->update([
                        'data' => $setting['value']
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
