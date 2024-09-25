<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends Controller
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

        $payments = Payment::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Payment/Index', [
            'payments' => fn() => PaymentResource::collection($payments),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        try {
            Payment::create($request->validated());

            return redirect()->back()->with('success', 'Data Pembayaran berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Payment $payment)
    {
        try {
            if (!$request->ajax()) return false;

            return response()->json([
                'messages' => 'Merk ditemukan',
                'data' => new PaymentResource($payment)
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
    public function update(PaymentRequest $request, Payment $payment)
    {
        try {
            $payment->update($request->validated());

            return redirect()->back()->with('success', 'Data Pembayaran berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();

            return redirect()->back()->with('success', 'Data Pembayaran berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Payment::destroy($request->ids);
            return to_route('backoffice.payment.index')->with('success', 'Data Pembayaran berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }
}
