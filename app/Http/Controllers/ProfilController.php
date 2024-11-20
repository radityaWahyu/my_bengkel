<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilRequest;
use App\Http\Resources\ProfilResource;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return inertia('User/UserFormProfile', [
            'user' => fn() => new ProfilResource($user)
        ]);
    }

    public function update(ProfilRequest $request, User $profil)
    {
        try {
            DB::transaction(function () use ($profil, $request) {


                $fields = $request->except('password', 'username');
                //dd($fields);

                $userFields = ['username' => $request->username];
                if (!empty($request->password)) $userFields += ['password' => $request->password];

                $profil->update($userFields);
                $profil->employee->update($fields);


                return redirect()->back()->with('success', 'Data profil berhasil di simpan.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
