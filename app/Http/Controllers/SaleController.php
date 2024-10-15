<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Jurnal;
use App\Models\Product;
use App\Models\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SaleResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SaleDetailResource;
use App\Http\Resources\SaleProductResource;

class SaleController extends Controller
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

        $sales = Sale::query()
            ->with(['sale_products' => ['product']])
            ->withCount(['sale_products'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('sale_code', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);



        return inertia('SaleTransaction/Index', [
            'sales' => fn() => SaleResource::collection($sales),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $sale = Sale::create([
                'status' => 'create',
                'user_id' => $request->user()->id,
            ]);

            return to_route('backoffice.sale.create-invoice', $sale->id);
        } catch (\Illuminate\Database\QueryException $exception) {
            return to_route('backoffice.sale.index')->with('error', $exception->errorInfo);
        }
    }


    public function createInvoice(Request $request, Sale $sale)
    {


        return inertia('SaleTransaction/SaleTransactionForm', [
            'sale' => fn() => new SaleResource($sale),
            'products' => fn() => ProductResource::collection($this->productList($request)),
            'sale_product' => fn() => SaleProductResource::collection($sale->sale_products)
        ]);
    }


    public function addProduct(Request $request, Sale $sale)
    {
        try {
            $product = Product::find($request->product_id);

            $productStock = $product->stock;

            if ($product->stock < $request->qty)
                return redirect()->back()->with('error', 'Stok Barang tidak mencukupi silahkan tambah stok terlebih dahulu.');

            DB::transaction(function () use ($sale, $request, $productStock, $product) {

                $sale_product = $sale->sale_products()
                    ->where('product_id', $request->product_id)->first();

                $qty = 0;

                if ($sale_product) {
                    $qty = $sale_product->qty + $request->qty;

                    $sale->sale_products()
                        ->where('id', $sale_product->id)
                        ->update([
                            'qty' => $qty,
                            'total' => $qty * $request->price
                        ]);
                } else {
                    $qty = $request->qty;
                    $sale->sale_products()->create(
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

    public function updateQtyProduct(Request $request, SaleProduct $sale_product)
    {
        try {

            $product = Product::find($sale_product->product_id);


            DB::transaction(function () use ($request, $sale_product, $product) {
                $productStock = $product->stock;
                $saleProductQty = $sale_product->qty;

                if ($saleProductQty > $request->qty) {
                    $deviation = $saleProductQty - $request->qty;



                    $sale_product->update([
                        'qty' => $request->qty,
                        'total' => $sale_product->price * $request->qty,
                    ]);

                    $product->update([
                        'stock' => $productStock + $deviation,
                    ]);
                } else {
                    $deviation =  $request->qty - $saleProductQty;

                    if ($product->stock < $deviation)
                        return redirect()->back()->with('error', 'Stok Barang tidak mencukupi silahkan tambah stok terlebih dahulu.');

                    $sale_product->update([
                        'qty' => $request->qty,
                        'total' => $sale_product->price * $request->qty,
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

    public function deleteServiceProduct(SaleProduct $sale_product)
    {
        try {
            $product = Product::find($sale_product->product_id);
            $productStock = $product->stock;

            DB::transaction(function () use ($sale_product, $product, $productStock) {
                $sale_product->delete();

                $product->update([
                    'stock' => $productStock + $sale_product->qty,
                ]);

                return redirect()->back()->with('success', 'Barang berhasil di hapus.');
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale, $request) {
                $jurnal_count = Jurnal::whereDate('created_at', Carbon::today())->count();
                $jurnal_code = 'JRNL' . Carbon::today()->format('dmY') . '' . str_pad($jurnal_count + 1, 3, '0', STR_PAD_LEFT);

                $sale_count = Sale::whereDate('created_at', Carbon::today())->count();
                $sale_code = 'INVOICE/SLS/' . Carbon::today()->format('dmY') . '/' . str_pad($sale_count + 1, 3, '0', STR_PAD_LEFT);

                $finished_at = Carbon::now();

                $sale->update([
                    'sale_code' => $sale_code,
                    'payment_id' => $request->payment_id,
                    'extra_pay' => $request->extra_pay,
                    'paid' => $request->paid,
                    'status' => 'finish',
                    'created_at' => $finished_at,
                    'updated_at' => $finished_at,
                ]);

                $sale->jurnals()->create([
                    'jurnal_code' => $jurnal_code,
                    'payment_id' => $request->payment_id,
                    'income' => $sale->total + $request->extra_pay,
                    'expense' => 0,
                    'description' => 'Pembayaran Transaksi Penjualan dengan kode' . $sale->sale_code,
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

    public function printInvoice(Sale $sale)
    {
        return inertia('SaleTransaction/SaleTransactionInvoice', [
            'setting' => fn() => $this->getSetting(),
            'sale' => fn() => new SaleDetailResource($sale)
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            $sale->delete();
            return redirect()->back()->with('success', 'Transaksi Servis berhasil dihapus.');
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
