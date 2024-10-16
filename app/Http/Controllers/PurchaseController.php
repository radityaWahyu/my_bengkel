<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Jurnal;
use App\Models\Product;
use App\Models\Purchase;
use App\Traits\Settings;
use App\Models\PurchaseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SaleDetailResource;
use App\Http\Resources\PurchaseProductResource;

class PurchaseController extends Controller
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

        $purchases = Purchase::query()
            ->with(['purchase_products' => ['product'], 'supplier'])
            ->withCount(['purchase_products'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('sale_code', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);



        return inertia('PurchaseTransaction/Index', [
            'purchases' => fn() => PurchaseResource::collection($purchases),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $purchase = Purchase::create([
                'status' => 'create',
                'user_id' => $request->user()->id,
            ]);

            return to_route('backoffice.purchase.create-invoice', $purchase->id);
        } catch (\Illuminate\Database\QueryException $exception) {
            return to_route('backoffice.purchase.index')->with('error', $exception->errorInfo);
        }
    }


    public function createInvoice(Request $request, Purchase $purchase)
    {


        return inertia('PurchaseTransaction/PurchaseTransactionForm', [
            'purchases' => fn() => new PurchaseResource($purchase),
            'products' => fn() => ProductResource::collection($this->productList($request)),
            'purchase_product' => fn() => PurchaseProductResource::collection($purchase->purchase_products)
        ]);
    }


    public function addProduct(Request $request, Purchase $purchase)
    {
        try {

            DB::transaction(function () use ($purchase, $request) {

                $purchase_product = $purchase->purchase_products()
                    ->where('product_id', $request->product_id)->first();

                $qty = 0;

                if ($purchase_product) {
                    $qty = $purchase_product->qty + $request->qty;

                    $purchase->purchase_products()
                        ->where('id', $purchase_product->id)
                        ->update([
                            'qty' => $qty,
                            'total' => $qty * $request->price
                        ]);
                } else {
                    $qty = $request->qty;
                    $purchase->purchase_products()->create(
                        [
                            'product_id' => $request->product_id,
                            'qty' => $qty,
                            'price' => $request->price,
                            'total' => $request->price * $request->qty
                        ]
                    );
                }

                return redirect()->back()->with('success', 'Barang berhasil di masukkan.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function updateQtyProduct(Request $request, PurchaseProduct $purchase_product)
    {
        try {

            $purchase_product->update([
                'qty' => $request->qty,
                'total' => $purchase_product->price * $request->qty,
            ]);

            return redirect()->back()->with('success', 'Jumlah berhasil di rubah.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    public function deleteServiceProduct(PurchaseProduct $purchase_product)
    {
        try {

            $purchase_product->delete();
            return redirect()->back()->with('success', 'Barang berhasil di hapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase, $request) {
                $jurnal_count = Jurnal::whereDate('created_at', Carbon::today())->count();
                $jurnal_code = 'JRNL' . Carbon::today()->format('dmY') . '' . str_pad($jurnal_count + 1, 3, '0', STR_PAD_LEFT);

                $purchase_count = Purchase::whereDate('created_at', Carbon::today())->count();
                $purchase_code = 'INVOICE/SLS/' . Carbon::today()->format('dmY') . '/' . str_pad($purchase_count + 1, 3, '0', STR_PAD_LEFT);

                $finished_at = Carbon::now();

                $purchase->update([
                    'sale_code' => $purchase_code,
                    'payment_id' => $request->payment_id,
                    'extra_pay' => $request->extra_pay,
                    'paid' => $request->paid,
                    'total' => $request->total,
                    'status' => 'finish',
                    'created_at' => $finished_at,
                    'updated_at' => $finished_at,
                    'user_id' => $request->user()->id,
                ]);

                $purchase->jurnals()->create([
                    'jurnal_code' => $jurnal_code,
                    'payment_id' => $request->payment_id,
                    'income' => $purchase->total + $request->extra_pay,
                    'expense' => 0,
                    'description' => 'Pembayaran Transaksi Penjualan dengan kode' . $purchase->sale_code,
                    'transaction_date' => $finished_at,
                    'user_id' => $request->user()->id,
                ]);


                return redirect()->back()->with('success', 'Transaksi Penjualan telah telah selesai di bayar');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    public function printInvoice(Purchase $purchase)
    {
        return inertia('PurchaseTransaction/PurchaseTransactionInvoice', [
            'setting' => fn() => $this->getSetting(),
            'sale' => fn() => new SaleDetailResource($purchase)
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase, $request) {


                $purchase_products = $purchase->purchase_products;

                // dd($purchase_products->count());
                if ($purchase_products->count() > 0) {
                    foreach ($purchase_products as $product) {
                        $product_data = Product::find($product->product_id);
                        $old_stock = $product_data->stock;
                        $product_data->update([
                            'stock' => $old_stock + $product->qty,
                        ]);
                    }
                }

                $purchase->jurnals()->where('transactable_id', $purchase->id)->delete();

                $purchase->delete();
            });

            return redirect()->back()->with('success', 'Transaksi Penjualan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function cancelSale(Purchase  $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {

                $purchase_products = $purchase->purchase_products;

                if ($purchase_products->count() > 0) {
                    foreach ($purchase_products as $product) {
                        $product_data = Product::find($product->product_id);
                        $old_stock = $product_data->stock;
                        $product_data->update([
                            'stock' => $old_stock + $product->qty,
                        ]);
                    }
                }


                $purchase->delete();
            });

            return redirect()->back()->with('success', 'Transaksi Penjualan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function productList(Request $request)
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

        $products = Product::query()
            ->with(['category', 'rack'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                if ($request->sortName == 'category')
                    return $query->orderBy('category_id', $request->sortType);

                if ($request->sortName == 'rack')
                    return $query->orderBy('rack_id', $request->sortType);

                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return $products;
    }
}
