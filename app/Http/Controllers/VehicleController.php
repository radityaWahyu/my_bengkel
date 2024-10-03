<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\VehicleRequest;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\VehicleResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\VehicleEditResource;

class VehicleController extends Controller
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

        $vehicles = Vehicle::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Vehicle/Index', [
            'customers' => fn() => VehicleResource::collection($vehicles),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::get();
        $brands = Brand::get();

        if ($request->has('customer')) {
            $customer = $customers->find($request->customer);

            return inertia('Customer/VehicleForm', [
                'customers' => fn() => CustomerResource::collection($customers),
                'customer' => fn() => new CustomerResource($customer),
                'brands' => fn() => BrandResource::collection($brands),
                'redirect' => $request->redirect,
            ]);
        }

        return inertia('Customer/VehicleForm', [
            'customers' => fn() => CustomerResource::collection($customers),
            'brands' => fn() => BrandResource::collection($brands),
            'redirect' => $request->redirect,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        try {
            Vehicle::create($request->validated());

            if ($request->redirect == 'customer')
                return to_route('backoffice.customer.index')->with('success', 'Data pelanggan dan Kendaraan berhasil disimpan');

            if ($request->redirect == 'customer-detail')
                return to_route('backoffice.customer.show', $request->customer_id)->with('success', 'Data Kendaraan berhasil disimpan');

            return redirect()->back()->with('success', 'Data Kendaraan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Vehicle $vehicle)
    {
        $customers = Customer::get();
        $brands = Brand::get();


        return inertia('Customer/VehicleForm', [
            'customers' => fn() => CustomerResource::collection($customers),
            'customer' => fn() => new CustomerResource($vehicle->customer),
            'vehicle' => fn() => new VehicleEditResource($vehicle),
            'brands' => fn() => BrandResource::collection($brands),
            'redirect' => $request->redirect,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        try {
            $vehicle->update($request->validated());

            return to_route('backoffice.customer.show', $request->customer_id)->with('success', 'Data Kendaraan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        try {
            $vehicle->delete();
            return redirect()->back()->with('success', 'Data Kendaraan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Vehicle::destroy($request->ids);
            return to_route('backoffice.customer.index')->with('success', 'Data Kendaraan berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function getVehicleLists(Request $request)
    {
        $perPage = 10;
        $params = [];

        if ($request->has('search')) {
            $params += ['search' => $request->search];
        } else {
            $params += ['search' => null];
        }

        if ($request->has('perPage')) $perPage = $request->perPage;

        $vehicles = Vehicle::query()
            ->with(['customer'])
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->whereHas('customer', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('plate_number', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return VehicleResource::collection($vehicles);
    }
}
