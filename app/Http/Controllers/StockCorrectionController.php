<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StockCorrection;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StockCorrectionRequest;
use App\Http\Resources\StockCorrectionResource;
use App\Models\Product;

class StockCorrectionController extends Controller
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

        if ($request->has('start_date') && $request->has('end_date')) {
            $params += ['start_date' => $request->start_date, 'end_date' => $request->end_date];
        } else {
            $params += ['start_date' => null, 'end_date' => null];
        }

        if ($request->has('perPage')) $perPage = $request->perPage;

        $stock_corrections = StockCorrection::query()
            ->with(['product', 'user' => ['employee']])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when(
                $request->has('start_date') && $request->has('end_date'),
                function ($query) use ($request) {
                    return $query->whereDate('created_at', '>=', $request->start_date)
                        ->whereDate('created_at', '<=', $request->end_date);
                }
            )
            ->when(
                $request->missing('start_date') && $request->missing('end_date'),
                function ($query) use ($request) {
                    return $query->whereMonth('created_at', Carbon::today()->format('m'));
                }
            )
            ->latest()->paginate($perPage);

        return inertia('StockCorrection/Index', [
            'stock_corrections' => fn() => StockCorrectionResource::collection($stock_corrections),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('StockCorrection/StockCorrectionForm');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockCorrectionRequest $request)
    {

        try {
            DB::transaction(function () use ($request) {

                $fieldValues = $request->validated() +  ['user_id' => $request->user()->id];
                StockCorrection::create($fieldValues);

                Product::find($request->product_id)
                    ->update([
                        'stock' => $request->new_stock,
                    ]);
            });

            return to_route('backoffice.stock-correction.index')
                ->with('success', 'Data Perbaikan stok berhasil di simpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockCorrection $stock_correction)
    {
        try {
            $stock_correction->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
