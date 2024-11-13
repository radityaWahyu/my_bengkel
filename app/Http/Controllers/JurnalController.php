<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use App\Http\Requests\JurnalRequest;
use App\Http\Resources\JurnalResource;
use App\Http\Resources\JurnalEditResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JurnalController extends Controller
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

        $jurnals = Jurnal::query()
            ->with(['user' => ['employee']])
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
            // ->when(
            //     $request->missing('start_date') && $request->missing('end_date'),
            //     function ($query) use ($request) {
            //         return $query->whereMonth('transaction_date', Carbon::today()->format('m'));
            //     }
            // )
            ->latest('id')->paginate($perPage);

        return inertia('Jurnal/Index', [
            'jurnals' => fn() => JurnalResource::collection($jurnals),
            'params' => fn() => (object)$params,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(JurnalRequest $request)
    {
        try {
            $jurnal_count = Jurnal::whereDate('created_at', Carbon::today())->count();
            $jurnal_code = 'JRNL' . Carbon::today()->format('dmY') . '' . str_pad($jurnal_count + 1, 3, '0', STR_PAD_LEFT);

            $jurnal = new Jurnal();
            $jurnal->jurnal_code = $jurnal_code;
            $jurnal->transaction_date = $request->transaction_date;
            $jurnal->payment_id = $request->payment;

            if ($request->jurnal_type == 'expense') {
                $jurnal->expense = $request->total;
                $jurnal->income = 0;
            } else {
                $jurnal->expense = 0;
                $jurnal->income = $request->total;
            }

            $jurnal->description = $request->description;
            $jurnal->user_id = $request->user()->id;
            $jurnal->save();

            return to_route('backoffice.jurnal.index')
                ->with('success', 'Jurnal berhasil di simpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurnal $jurnal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Jurnal $jurnal)
    {
        try {
            if (!$request->ajax()) return false;

            return response()->json([
                'messages' => 'Jurnal ditemukan',
                'data' => new JurnalEditResource($jurnal)
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
    public function update(Request $request, Jurnal $jurnal)
    {
        try {
            $jurnal->transaction_date = $request->transaction_date;
            $jurnal->payment_id = $request->payment;

            if ($request->jurnal_type == 'expense') {
                $jurnal->expense = $request->total;
                $jurnal->income = 0;
            } else {
                $jurnal->expense = 0;
                $jurnal->income = $request->total;
            }

            $jurnal->description = $request->description;
            $jurnal->user_id = $request->user()->id;
            $jurnal->save();

            return to_route('backoffice.jurnal.index')
                ->with('success', 'Jurnal berhasil di simpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
    {
        try {
            $jurnal->delete();

            return redirect()->back()->with('success', 'Jurnal berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }
}
