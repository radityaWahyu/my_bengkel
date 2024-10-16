<?php

namespace App\Http\Controllers;


use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;

class SupplierController extends Controller
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

        $suppliers = Supplier::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('contact_name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Supplier/Index', [
            'suppliers' => fn() => SupplierResource::collection($suppliers),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Supplier/SupplierForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        try {
            Supplier::create($request->validated());

            return to_route('backoffice.supplier.index')->with('success', 'Data Pemasok berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return inertia('Supplier/SupplierForm', [
            'supplier' => fn() => new SupplierResource($supplier)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        try {
            $supplier->update($request->validated());

            return to_route('backoffice.supplier.index')->with('success', 'Data Pemasok berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return redirect()->back()->with('success', 'Data Pemasok berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Supplier::destroy($request->ids);
            return to_route('backoffice.supplier.index')->with('success', 'Data Pemasok berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function getSupplierLists(Request $request)
    {
        $perPage = 10;

        if ($request->has('perPage')) $perPage = $request->perPage;

        $suppliers = Supplier::query()
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return SupplierResource::collection($suppliers);
    }
}
