<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Illuminate\Http\Request;
use App\Http\Requests\RepairRequest;
use App\Http\Resources\RepairResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RepairController extends Controller
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

        $repairs = Repair::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Repair/Index', [
            'repairs' => fn() => RepairResource::collection($repairs),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RepairRequest $request)
    {
        try {
            Repair::create($request->validated());

            return redirect()->back()->with('success', 'Data Perbaikan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Repair $repair)
    {
        try {
            if (!$request->ajax()) return false;

            return response()->json([
                'messages' => 'Merk ditemukan',
                'data' => new RepairResource($repair)
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'messages' => $exception,
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RepairRequest $request, Repair $repair)
    {
        try {
            $repair->update($request->validated());

            return redirect()->back()->with('success', 'Data Perbaikan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Repair $repair)
    {
        try {
            $repair->delete();

            return redirect()->back()->with('success', 'Data Perbaikan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Repair::destroy($request->ids);
            return to_route('backoffice.repair.index')->with('success', 'Data Perbaikan berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function getRepairLists(Request $request)
    {
        $perPage = 10;

        if ($request->has('perPage')) $perPage = $request->perPage;

        $repairs = Repair::query()
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return RepairResource::collection($repairs);
    }
}
