<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\PaymentMethod;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use ApiResponser;

    public function index() {
        $paymentMethods = PaymentMethod::all();

        if (!empty($paymentMethods)) {
            return $this->success($paymentMethods, "success", 200);
        } else {
            return $this->error("error", "error", 500);
        }
    }


    public function paymentMethodsStatistics(){
        $paymentMethodsStatistics = [];

        $total_cash = Bill::where("payment_method_id", "=", 1)->get();
        $total_transfer = Bill::where("payment_method_id", "=", 2)->get();
        $total_fragments = Bill::where("payment_method_id", "=", 3)->get();
        $paymentMethodsStatistics["total_cash"] = $total_cash;
        $paymentMethodsStatistics["total_transfer"] = $total_transfer;
        $paymentMethodsStatistics["total_fragments"] =  $total_fragments;

        foreach ($paymentMethodsStatistics as $key => $salesStatistic) {
            $paymentMethodsStatistics[$key] = $salesStatistic->map(function($item,$key) {
                return  ($item["original_total"] - ($item["original_total"] * $item["applied_discount"])) - $item["total_returned"];
            })
                ->sum();
        }

        return $this->success($paymentMethodsStatistics, "success", 200);

    }
}
