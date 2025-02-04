<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Jurnal;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceRepair;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function index(Request $request)
    {

        $user_level = $request->user()->getRoleNames()[0];

        if ($user_level == 'mekanik') return $this->mechanicDashboard($request->user());

        return $this->mainDashboard();
    }

    public function mechanicDashboard($user)
    {
        $repair = ServiceRepair::select('id', 'finished_at')
            ->where('employee_id', $user->employee_id)->get();

        return inertia('Dashboard/DashboardMechanic', [
            'count_repair' => Inertia::lazy(fn() => $repair->whereNull('finished_at')->count()),
            'count_finished_repair' => Inertia::lazy(fn() => $repair->whereNotNull('finished_at')->count()),
        ]);
    }


    public function mainDashboard()
    {

        return inertia('Dashboard/DashboardOperator', [
            'saldo' => Inertia::lazy(fn() => $this->getSaldo()),
            'service_finished' => Inertia::lazy(fn() => $this->services()),
            'service_count' => Inertia::lazy(fn() => $this->services(false)),
            'customer_count' => Inertia::lazy(fn() => Customer::select('id')->count()),
            'income_now' => Inertia::lazy(fn() => $this->getTransactionNow()[0]->total_income ?? 0),
            'expense_now' => Inertia::lazy(fn() => $this->getTransactionNow()[0]->total_expense ?? 0),
        ]);
    }

    public function getSaldo()
    {
        $jurnal = Jurnal::select(
            DB::raw('SUM(income)AS total_income'),
            DB::raw('SUM(expense)AS total_expense'),
        )->get();

        return $jurnal[0]->total_income - $jurnal[0]->total_expense;
    }

    public function getTransactionNow()
    {
        return Jurnal::select(
            DB::raw('SUM(income)AS total_income'),
            DB::raw('SUM(expense)AS total_expense'),
        )
            ->whereMonth('created_at', Carbon::today()->format('m'))
            ->get();
    }

    public function services($done = true)
    {
        if ($done)
            return Service::select('id')
                ->whereNotNull('finished_date')
                ->whereMonth('finished_date', Carbon::today()->format('m'))
                ->count();

        return
            Service::select('id')
            ->whereNull('finished_date')
            ->whereMonth('created_at', Carbon::today()->format('m'))
            ->count();
    }
}
