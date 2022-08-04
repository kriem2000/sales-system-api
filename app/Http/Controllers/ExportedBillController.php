<?php

namespace App\Http\Controllers;

use App\Models\ExportedBill;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExportedBillController extends Controller
{
    use ApiResponser;

    public function create(Request $request) {
        $data = Validator::make($request->all(),[
            "billName" => "required|max:255|unique:exported_bills,bill_name",
            "exporterName" => "required|max:255",
            "billDate" => "nullable|date",
            "exporterPhone" => "nullable|min:10",
        ]);

       if (! $data->fails()) {
           $data = $data->validated();
           ExportedBill::create([
               "bill_name" => $data["billName"],
               "exporter_name" => $data["exporterName"],
               "exporter_phone" => $data["exporterPhone"] ?? "",
               "bill_date" => $data["billDate"] ?? "",
               "created_by" => auth()->user()->id,
           ]);
           return $this->success($data, "تمت العملية بنجاح", 200);
       } else {
           return $this->error("الرجاء مراجعة المدخلات", $data->validated(), 400);
       }
    }

    public function index() {
        return $this->success(ExportedBill::all(), "success", 200);
    }
}
