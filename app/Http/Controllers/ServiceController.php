<?php

namespace App\Http\Controllers;


use App\Models\Service;

use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceDetailResource;
use App\Http\Resources\ServiceResource;


class ServiceController extends Controller
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

        $services = Service::query()
            ->with(['user' => ['employee'], 'vehicle' => ['customer']])
            ->withCount(['products', 'repairs'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('ServiceTransaction/Index', [
            'services' => fn() => ServiceResource::collection($services),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return inertia('ServiceTransaction/ServiceTransactionForm');
    }

    public function createInvoice(Service $service)
    {
        return inertia('ServiceTransaction/ServiceTransactionCheckingForm', [
            'service' => fn() => new ServiceDetailResource($service)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        try {
            $field_validated = $request->validated() + ['status' => 'waiting'];
            Service::create($field_validated);

            return to_route('backoffice.service.index')->with('success', 'Transaksi Servis berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        try {
            $service->update($request->validated());

            return to_route('backoffice.service.index')->with('success', 'Transaksi Servis berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            $service->delete();
            return redirect()->back()->with('success', 'Transaksi Servis berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Service::destroy($request->ids);
            return to_route('backoffice.service.index')->with('success', 'Transaksi Servis berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function addRepair(Request $request, Service $service)
    {


        try {
            $service->service_repairs()->create(
                [
                    'service_id' => $service->id,
                    'repair_id' => $request->repair_id,
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'total' => $request->price * $request->qty
                ]
            );

            return redirect()->back()->with('success', 'Jenis Perbaikan berhasil di masukkan.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function addProduct(Request $request, Service $service)
    {
        try {
            $service->products()->attach(
                $request->product_id,
                [
                    'qty' => $request->qty,
                    'price' => $request->price,
                    'total' => $request->price * $request->qty
                ]
            );

            return redirect()->back()->with('success', 'Barang berhasil di masukkan.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
