<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\FragmentedBill;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FragmentedBillController extends Controller
{
    use ApiResponser;

    public function changeStatus(FragmentedBill $id) {
        $id->fragment_bill_status_id = 1;
        $id->payment_date = Carbon::now();
        $id->save();
        $bill = Bill::with("fragmented_bill")->where("id", $id->bill_id)->get();
        return $this->success($bill, "success", 200);
    }

    public function fragmentPaymentsStatistics() {
        $fragmentPaymentsStatics = [];
        $total_paid = FragmentedBill::where("fragment_bill_status_id", "=", 1 )
            ->get()
            ->pluck("payment_amount")
            ->sum();
        $total_unpaid = FragmentedBill::where("fragment_bill_status_id", "!=", 1 )
            ->get()
            ->pluck("payment_amount")
            ->sum();
        $fragmentPaymentsStatics["total_paid"] = $total_paid;
        $fragmentPaymentsStatics["total_unpaid"] = $total_unpaid;
        return $this->success($fragmentPaymentsStatics, "success", 200);
    }

}
