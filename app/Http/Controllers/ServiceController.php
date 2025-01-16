<?php

namespace App\Http\Controllers;


use Carbon\Carbon;

use App\Models\Jurnal;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Traits\Settings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceRepair;
use App\Models\ServiceProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceReceipt;
use App\Http\Resources\ServiceResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ServiceDetailResource;
use App\Http\Resources\ServiceMechanicResource;
use App\Http\Resources\ServiceReceiptResource;

class ServiceController extends Controller
{
    use Settings;
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
            ->withCount(['service_repairs', 'service_products'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->whereHas('vehicle', function ($query) use ($request) {
                    return $query->where('plate_number', 'like', '%' . $request->search . '%')
                        ->orWhereHas('customer', function ($query) use ($request) {
                            return $query->where('name', 'like', '%' . $request->search . '%');
                        });
                });
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
        return inertia('ServiceTransaction/ServiceTransactionEditForm', [
            'service' => fn() => new ServiceDetailResource($service)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        try {
            $field_validated = $request->validated() + ['status' => 'waiting', 'user_id' => $request->user()->id];
            Service::create($field_validated);

            return to_route('backoffice.service.index')->with('success', 'Transaksi Servis berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function edit(Service $service)
    {
        return inertia('ServiceTransaction/ServiceTransactionEditForm', [
            'service' => fn() => new ServiceDetailResource($service),
            'edit' => fn() => true,
        ]);
    }

    public function show(Service $service)
    {
        return inertia('ServiceTransaction/ServiceTransactionDetail', [
            'setting' => fn() => $this->getSetting(),
            'service' => fn() => new ServiceDetailResource($service)
        ]);
    }

    public function update(Request $request, Service $service)
    {
        try {
            switch ($request->status) {
                case 'finish':

                    DB::transaction(function () use ($service, $request) {
                        $jurnal_count = Jurnal::whereDate('created_at', Carbon::today())->count();
                        $jurnal_code = 'JRNL' . Carbon::today()->format('dmY') . '' . str_pad($jurnal_count + 1, 3, '0', STR_PAD_LEFT);



                        $service->update([
                            'payment_id' => $request->payment_id,
                            'extra_pay' => $request->extra_pay,
                            'paid' => $request->paid,
                            'status' => 'finish',
                            'notes' => $request->notes,
                            'finished_date' => Carbon::now(),
                        ]);

                        $service->jurnals()->create([
                            'jurnal_code' => $jurnal_code,
                            'income' => $service->total + $request->extra_pay,
                            'expense' => 0,
                            'description' => 'Pembayaran Transaksi service dengan kode' . $service->service_code . 'oleh pelanggan' . $service->vehicle->customer->name,
                            'transaction_date' => $service->created_at,
                            'user_id' => $request->user()->id,
                            'payment_id' => $request->payment_id,
                        ]);


                        return redirect()->back()->with('success', 'Transaksi Service telah telah selesai di bayar');
                    });

                    break;

                case 'pending':
                    $service->update([
                        'status' => 'pending',
                        'notes' => $request->notes,
                    ]);

                    return to_route('backoffice.service.index')->with('success', 'Transaksi berhasil di simpan');

                    break;
                default:
                    return to_route('backoffice.service.index');
                    break;
            }
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function approvedService(Request $request, Service $service)
    {
        try {
            $service_count = Service::whereDate('created_at', Carbon::today())->count();
            $service_code = 'INVOICE/SRV/' . Carbon::today()->format('dmY') . '/' . str_pad($service_count + 1, 3, '0', STR_PAD_LEFT);

            $service->update([
                'service_code' => $service_code,
                'status' => 'approved',
                'total' => $request->total,
                'created_at' => Carbon::today(),
                'updated_at' => Carbon::today(),
            ]);

            return to_route('backoffice.service.index')->with('success', 'Transaksi Servis berhasil dibuat invoice');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function printReceipt(Service $service)
    {
        $service->with(['vehicle']);

        return inertia('ServiceTransaction/ServiceTransactionReceipt', [
            'setting' => fn() => $this->getSetting(),
            'service' => fn() => new ServiceReceiptResource($service)
        ]);
    }

    public function printInvoice(Service $service)
    {

        return inertia('ServiceTransaction/ServiceTransactionInvoice', [
            'setting' => fn() => $this->getSetting(),
            'service' => fn() => new ServiceDetailResource($service)
        ]);
    }

    public function customerServiceDetail(Service $service)
    {
        return inertia('ServiceTransaction/ServiceTransactionCustomerDetail', [
            'service' => fn() => new ServiceDetailResource($service),
        ]);
    }

    public function destroy(Request $request, Service $service)
    {
        try {
            DB::transaction(function () use ($service, $request) {

                $service_products = $service->service_products;

                // dd($service_products->count());
                if ($service_products->count() > 0) {
                    foreach ($service_products as $product) {
                        $product_data = Product::find($product->product_id);
                        $old_stock = $product_data->stock;
                        $product_data->update([
                            'stock' => $old_stock + $product->qty,
                        ]);
                    }
                }

                $service->jurnals()->where('transactable_id', $service->id)->delete();

                $service->delete();

                return redirect()->back()->with('success', 'Transaksi Service berhasil dihapus.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function addRepair(Request $request, Service $service)
    {


        try {
            $repair = $service->service_repairs()->where('repair_id', $request->repair_id)->count();

            if ($repair) return redirect()->back()->with('error', 'Jenis Perbaikan sudah terinput pada sistem ');

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

    public function startRepair(ServiceRepair $service_repair)
    {

        try {
            DB::transaction(function () use ($service_repair) {
                $service_repair->update([
                    'started_at' => Carbon::now(),
                ]);

                $service = Service::find($service_repair->service_id);

                if ($service->status == 'approved') {
                    $service->update(['status' => 'process']);
                }

                return redirect()->back()->with('success', 'Perbaikan kendaraan dimulai');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function finishRepair(ServiceRepair $service_repair)
    {
        try {
            DB::transaction(function () use ($service_repair) {
                $service_repair->update([
                    'finished_at' => Carbon::now(),
                ]);

                return redirect()->back()->with('success', 'Perbaikan telah selesai');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function addEmployee(Request $request, ServiceRepair $service_repair)
    {
        try {
            $service_repair->update([
                'employee_id' => $request->employee_id
            ]);

            return redirect()->back()->with('success', 'Mekanik berhasil di input ke sistem.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteServiceRepair(ServiceRepair $service_repair)
    {
        try {
            $service_repair->delete();

            return redirect()->back()->with('success', 'Jenis Perbaikan berhasil di hapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function addProduct(Request $request, Service $service)
    {
        try {
            $product = Product::find($request->product_id);

            $productStock = $product->stock;

            if ($product->stock < $request->qty)
                return redirect()->back()->with('error', 'Stok Barang tidak mencukupi silahkan tambah stok terlebih dahulu.');

            DB::transaction(function () use ($service, $request, $productStock, $product) {

                $service_product = $service->service_products()
                    ->where('product_id', $request->product_id)->first();

                $qty = 0;

                if ($service_product) {
                    $qty = $service_product->qty + $request->qty;

                    $service->service_products()
                        ->where('id', $service_product->id)
                        ->update([
                            'qty' => $qty,
                            'total' => $qty * $request->price
                        ]);
                } else {
                    $qty = $request->qty;
                    $service->service_products()->create(
                        [
                            'product_id' => $request->product_id,
                            'qty' => $qty,
                            'price' => $request->price,
                            'total' => $request->price * $request->qty
                        ]
                    );
                }

                $product->update([
                    'stock' => $productStock - $request->qty,
                ]);

                return redirect()->back()->with('success', 'Barang berhasil di masukkan.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function updateQtyProduct(Request $request, ServiceProduct $service_product)
    {
        try {

            $product = Product::find($service_product->product_id);


            DB::transaction(function () use ($request, $service_product, $product) {
                $productStock = $product->stock;
                $serviceProductQty = $service_product->qty;

                if ($serviceProductQty > $request->qty) {
                    $deviation = $serviceProductQty - $request->qty;

                    $service_product->update([
                        'qty' => $request->qty,
                        'total' => $service_product->price * $request->qty,
                    ]);

                    $product->update([
                        'stock' => $productStock + $deviation,
                    ]);
                } else {
                    $deviation =  $request->qty - $serviceProductQty;

                    $service_product->update([
                        'qty' => $request->qty,
                        'total' => $service_product->price * $request->qty,
                    ]);

                    $product->update([
                        'stock' => $productStock - $deviation,
                    ]);
                }

                return redirect()->back()->with('success', 'Jumlah berhasil di rubah.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function deleteServiceProduct(ServiceProduct $service_product)
    {
        try {
            $product = Product::find($service_product->product_id);
            $productStock = $product->stock;

            DB::transaction(function () use ($service_product, $product, $productStock) {
                $service_product->delete();

                $product->update([
                    'stock' => $productStock + $service_product->qty,
                ]);

                return redirect()->back()->with('success', 'Barang berhasil di hapus.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function listServiceForMechanic(Request $request)
    {

        // $perPage = 10;
        $params = [];


        if ($request->has('perPage')) $perPage = $request->perPage;


        $repairs = ServiceRepair::query()
            ->join('services', 'services.id', '=', 'service_repairs.service_id')
            ->join('vehicles', 'vehicles.id', '=', 'services.vehicle_id')
            ->join('customers', 'customers.id', '=', 'vehicles.customer_id')
            ->select(
                'service_repairs.id as repair_id',
                'service_repairs.service_id',
                'services.service_code',
                'vehicles.plate_number',
                'customers.name as customer_name',
                'services.description as service_description',
                'service_repairs.started_at',
                'service_repairs.finished_at'
            )
            ->where('employee_id', $request->user()->employee->id)
            ->get();




        // $repairs = ServiceRepair::query()
        //     ->with(['service' => ['vehicle' => 'customer'], 'employee'])
        //     ->whereHas('employee', function ($query) use ($request) {
        //         return $query->where('employee_id', $request->user()->employee_id);
        //     })
        //     ->latest('id');



        return inertia('Mechanic/Index', [
            'repairs' => fn() => ServiceMechanicResource::collection($repairs),
            'params' => fn() => (object)$params,
        ]);
    }
}
