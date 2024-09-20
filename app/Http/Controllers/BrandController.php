<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BrandController extends Controller
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

        $brands = Brand::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Brand/Index', [
            'brands' => fn() => BrandResource::collection($brands),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        try {
            Brand::create($request->validated());

            return redirect()->back()->with('success', 'Data Merk berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Brand $brand)
    {
        try {
            if (!$request->ajax()) return false;

            return response()->json([
                'messages' => 'Merk ditemukan',
                'data' => new BrandResource($brand)
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
    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $brand->update($request->validated());

            return redirect()->back()->with('success', 'Data Merk berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();

            return redirect()->back()->with('success', 'Data Merk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Brand::destroy($request->ids);
            return to_route('backoffice.brand.index')->with('success', 'Data Merk berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }
}
