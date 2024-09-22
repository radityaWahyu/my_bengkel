<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\RackResource;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductEditResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
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

        return inertia('Product/Index', [
            'products' => fn() => ProductResource::collection($products),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $categories = Category::get();
        $racks = Rack::get();

        return inertia('Product/ProductForm', [
            'categories' => fn() => CategoryResource::collection($categories),
            'racks' => fn() => RackResource::collection($racks),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            Product::create($request->validated());

            return to_route('backoffice.product.index')->with('success', 'Data Barang berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::get();
        $racks = Rack::get();

        return inertia('Product/ProductForm', [
            'categories' => fn() => CategoryResource::collection($categories),
            'racks' => fn() => RackResource::collection($racks),
            'product' => fn() => new ProductEditResource($product)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());

            return to_route('backoffice.product.index')->with('success', 'Data Barang berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->back()->with('success', 'Data Barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Product::destroy($request->ids);
            return to_route('backoffice.product.index')->with('success', 'Data Barang berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }
}
