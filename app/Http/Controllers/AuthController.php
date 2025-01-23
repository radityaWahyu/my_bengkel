<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{


    public function index()
    {
        if (Auth::check()) return to_route('backoffice.dashboard.index');

        return inertia('Login',);
    }

    public function store(LoginRequest $request)
    {

        $credentials = $request->only('username', 'password') + ['is_enabled' => true];


        if (!Auth::attempt($credentials)) {
            return redirect()->back()->with('error', 'Username dan password salah dan tidak ditemukan dalam sistem');
        }

        $request->session()->regenerate();

        return to_route('backoffice.dashboard.index')->with('success', 'Selamat datang ' . $request->username);
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        return to_route('backoffice.auth.login')->with('success', 'Anda berhasil keluar dari sistem');
    }
}
