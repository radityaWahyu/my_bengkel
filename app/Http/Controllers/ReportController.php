<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Purchase;
use App\Traits\Settings;
use Illuminate\Http\Request;
use App\Http\Resources\SaleResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\PurchaseResource;
use App\Exports\ReportSaleTransactionExport;
use App\Http\Resources\ServiceDetailResource;
use App\Exports\ReportServiceTransactionExport;
use App\Exports\ReportPurchaseTransactionExport;

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

    public function exportServiceReport(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            return (new ReportServiceTransactionExport($request->start_date, $request->end_date))->download('laporan_service.xlsx');
        }

        return (new ReportServiceTransactionExport())->download('laporan_service.xlsx');
    }

    public function getSaleReport(Request $request, $paginate = true)
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

        $sales = Sale::query()
            ->with(['sale_products' => ['product']])
            ->withCount(['sale_products'])
            ->when($request->has('start_date') && $request->has('end_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->start_date)
                    ->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->missing('start_date') && $request->missing('end_date'), function ($query) use ($request) {
                return $query->whereMonth('created_at', Carbon::today()->format('m'));
            })
            ->latest();

        if ($paginate) {
            $sales = $sales->paginate($perPage);
        } else {
            $sales = $sales->get();
        }


        return ['sales' => $sales, 'params' => $params];
    }

    public function saleReport(Request $request)
    {

        $response = $this->getSaleReport($request);

        return inertia('Report/ReportSaleTransactions', [
            'sales' => fn() => SaleResource::collection($response['sales']),
            'params' => fn() => (object)$response['params'],
        ]);
    }

    public function printSaleReport(Request $request)
    {

        $response = $this->getSaleReport($request, false);

        return inertia('Report/ReportSaleTransactionPrint', [
            'sales' => fn() => SaleResource::collection($response['sales']),
            'setting' => fn() => $this->getSetting(),
            'params' => fn() => (object)$response['params'],
        ]);
    }

    public function exportSaleReport(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            return (new ReportSaleTransactionExport($request->start_date, $request->end_date))->download('laporan_penjualan.xlsx');
        }

        return (new ReportSaleTransactionExport())->download('laporan_penjualan.xlsx');
    }

    public function getPurchaseReport(Request $request, $paginate = true)
    {
        $perPage = 10;
        $params = [];

        if ($request->has('start_date') && $request->has('end_date')) {
            $params += ['start_date' => $request->start_date, 'end_date' => $request->end_date];
        } else {
            $params += ['start_date' => null, 'end_date' => null];
        }

        if ($request->has('perPage')) $perPage = $request->perPage;

        $purchases = Purchase::query()
            ->with(['purchase_products' => ['product'], 'supplier'])
            ->withCount(['purchase_products'])
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
            ->latest();

        if ($paginate) {
            $purchases = $purchases->paginate($perPage);
        } else {
            $purchases = $purchases->get();
        }


        return ['purchases' => $purchases, 'params' => $params];
    }

    public function purchaseReport(Request $request)
    {

        $response = $this->getPurchaseReport($request);

        return inertia('Report/ReportPurchaseTransactions', [
            'purchases' => fn() => PurchaseResource::collection($response['purchases']),
            'params' => fn() => (object)$response['params'],
        ]);
    }

    public function printPurchaseReport(Request $request)
    {

        $response = $this->getPurchaseReport($request, false);

        return inertia('Report/ReportPurchaseTransactionPrint', [
            'purchases' => fn() => PurchaseResource::collection($response['purchases']),
            'setting' => fn() => $this->getSetting(),
            'params' => fn() => (object)$response['params'],
        ]);
    }

    public function exportPurchaseReport(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            return (new ReportPurchaseTransactionExport($request->start_date, $request->end_date))->download('laporan_pembelian.xlsx');
        }

        return (new ReportPurchaseTransactionExport())->download('laporan_pembelian.xlsx');
    }

    public function serviceHistory(Request $request)
    {

        $perPage = 10;
        $params = [];



        if ($request->has('search')) {
            $params += ['search' => $request->search];
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
            ->when($request->missing('search'), function ($query) use ($request) {
                return $query->whereMonth('finished_date', Carbon::today()->format('m'));
            })
            ->where('status', 'finish');

        if ($request->missing('paginate')) {
            $services = $services->paginate($perPage);
        } else {
            $services = $services->get();
        }

        return inertia('History/HistoryServiceTransactions', [
            'services' => fn() => ServiceResource::collection($services),
            'params' => fn() => (object)$params,
        ]);
    }


    public function serviceDetailHistory(Service $service)
    {
        return inertia('History/HistoryServiceTransactionDetail', [
            'service' => fn() => new ServiceDetailResource($service),
            'edit' => fn() => true,
        ]);
    }
}
