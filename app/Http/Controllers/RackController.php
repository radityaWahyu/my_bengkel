<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use Illuminate\Http\Request;
use App\Http\Requests\RackRequest;
use App\Http\Resources\RackResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RackController extends Controller
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

        $racks = Rack::query()
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()->paginate($perPage);

        return inertia('Rack/Index', [
            'racks' => fn() => RackResource::collection($racks),
            'params' => fn() => (object)$params,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RackRequest $request)
    {
        try {
            Rack::create($request->validated());

            return redirect()->back()->with('success', 'Data Rak berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rack $rack)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Rack $rack)
    {
        try {
            if (!$request->ajax()) return false;
            return response()->json([
                'messages' => 'Kategori ditemukan',
                'data' => new RackResource($rack)
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
    public function update(RackRequest $request, Rack $rack)
    {
        try {
            $rack->update($request->validated());

            return redirect()->back()->with('success', 'Data Rak berhasil disimpan');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rack $rack)
    {
        try {
            $rack->delete();
            return redirect()->back()->with('success', 'Data Rak berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $exception) {
            return redirect()->back()->with('error', $exception->errorInfo);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            Rack::destroy($request->ids);
            return to_route('backoffice.rack.index')->with('success', 'Data Rack berhasil dihapus.');
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', $exception);
        }
    }
}
