<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReturnProductsController extends Controller
{
    use ApiResponser;
    public function returnsStatistics() {
        $allReturnsStatistics = [];
        $now = Carbon::now();

        $allReturns = Bill::all();

        $dailyReturns = Bill::whereDate("created_at", Carbon::today()->format("Y-m-d"))->get();

        $weeklyReturns = Bill::whereBetween(
            "created_at",
            [$now->startOfWeek()->format('Y-m-d'),  $now->endOfWeek()->format('Y-m-d')]
        )
            ->get();

        $monthlyReturns = Bill::whereDate(
            "created_at","like" ,
            Carbon::now()->format('Y')."-".Carbon::now()->format('m')."%"
        )
            ->get();

        $annualyReturns = Bill::whereDate("created_at", "like" ,Carbon::now()->format('Y')."%")->get();

        $allReturnsStatistics["dailyReturns"] = $dailyReturns;
        $allReturnsStatistics["allReturns"] = $allReturns;
        $allReturnsStatistics["weeklyReturns"] = $weeklyReturns;
        $allReturnsStatistics["monthlyReturns"] = $monthlyReturns;
        $allReturnsStatistics["annualyReturns"] = $annualyReturns;

        foreach ($allReturnsStatistics as $key => $salesStatistic) {
            $allReturnsStatistics[$key] = $salesStatistic->pluck("total_returned")->sum();
        }
        return $this->success($allReturnsStatistics, "success", 200);
    }

}
