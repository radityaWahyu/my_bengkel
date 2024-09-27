<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserEditResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $perPage = 10;
        $params = [];

        if ($request->has('sortName') && $request->has('sortType')) {
            $params += ['sortName' => $request->sortName, 'sortType' => $request->sortType];
        } else {
            $params += ['sortName' => null, 'sortType' => null];
        }

        if ($request->has('search')) {
            $params += ['search' => $request->search];
        } else {
            $params += ['search' => null];
        }

        if ($request->has('perPage')) $perPage = $request->perPage;

        $employees = User::query()
            ->with(['employee'])
            ->whereNot('username', 'administrator')
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()->paginate($perPage);

        // dd($employees);
        return inertia('User/Index', [
            'users' => fn() => UserResource::collection($employees),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::whereNotIn('name', ['administrator'])
            ->get();

        return inertia('User/UserForm', [
            'employees' => fn() => EmployeeResource::collection($employees)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            $user->assignRole($request->role);



            return to_route('backoffice.user.index')->with('success', 'Data User berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user $user)
    {
        $employees = Employee::whereNotIn('name', ['administrator'])
            ->get();

        return inertia('User/UserForm', [
            'employees' => fn() => EmployeeResource::collection($employees),
            'user' => fn() => new UserEditResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            $user->update($request->validated());

            $user->syncRoles($request->role);

            return to_route('backoffice.user.index')->with('success', 'Data User berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->back()->with('success', 'Data User berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            User::destroy($request->ids);
            return to_route('backoffice.user.index')->with('success', 'Data User berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function enabled(Request $request, User $user)
    {
        try {
            $user->update(['is_enabled' => $request->status]);

            return redirect()->route('backoffice.user.index')->with('success', 'Status User berhasil diubah');
        } catch (ModelNotFoundException $ex) {

            return redirect()->route('backoffice.user.index')->with('error', 'Data User tidak ditemukan.');
        } catch (\Illuminate\Database\QueryException $exception) {

            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
