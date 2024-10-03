<?php

namespace App\Http\Controllers;


use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerDetailResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class CustomerController extends Controller
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

        $customers = Customer::query()
            ->withCount(['vehicles'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Customer/Index', [
            'customers' => fn() => CustomerResource::collection($customers),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Customer/CustomerForm');
    }

    public function show(Customer $customer)
    {
        return inertia('Customer/CustomerDetail', [
            'customer' => fn() => new CustomerDetailResource($customer),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        try {
            //dd($request->validated());
            $employee = Customer::create($request->validated());

            return to_route('backoffice.vehicle.create', ['customer' => $employee->id, 'redirect' => 'customer'])->with('success', 'Data Pelanggan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return inertia('Customer/CustomerForm', [
            'customer' => fn() => new CustomerResource($customer)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            $customer->update($request->validated());

            return to_route('backoffice.customer.index')->with('success', 'Data Pelanggan berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return redirect()->back()->with('success', 'Data Pelanggan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Employee::destroy($request->ids);
            return to_route('backoffice.customer.index')->with('success', 'Data Pelanggan berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function getVehicles(Request $request)
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
                return $query->where('plate_number', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return response()->json([
            'data' => VehicleResource::collection($vehicles)
        ]);
    }
}
