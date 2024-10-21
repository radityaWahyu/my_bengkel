<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Traits\Settings;

class ReportController extends Controller
{

    use Settings;

    public function getServiceReport(Request $request, $paginate = true)
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

        $services = Service::query()
            ->with(['user' => ['employee'], 'vehicle' => ['customer']])
            ->withCount(['service_repairs', 'service_products'])
            ->when($request->has('sortName'), function ($query) use ($request) {
                return $query->orderBy($request->sortName, $request->sortType);
            })
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                return $query->whereDate('finished_date', '>=', $request->start_date)
                    ->whereDate('finished_date', '<=', $request->end_date);
            })
            ->when($request->missing('start_date') && $request->missing('end_date'), function ($query) use ($request) {
                return $query->whereMonth('finished_date', Carbon::today()->format('m'));
            })
            ->where('status', 'finish');

        if ($paginate) {
            $services = $services->paginate($perPage);
        } else {
            $services = $services->get();
        }

        return ['services' => $services, 'params' => $params];
    }
    public function serviceReport(Request $request)
    {

        $response = $this->getServiceReport($request);

        return inertia('Report/ReportServiceTransactions', [
            'services' => fn() => ServiceResource::collection($response['services']),
            'params' => fn() => (object)$response['params'],
        ]);
    }

    public function printServiceReport(Request $request)
    {
        $response = $this->getServiceReport($request, false);

        return inertia('Report/ReportServiceTransactionPrint', [
            'services' => fn() => ServiceResource::collection($response['services']),
            'setting' => fn() => $this->getSetting(),
            'params' => fn() => (object)$response['params'],
        ]);
    }
}
