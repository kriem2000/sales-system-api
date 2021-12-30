<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    use ApiResponser;
    public function create(Request $request) {
        $data = Validator::make($request->all(), [
            "id" => "required|unique:products,id",
            "name" => "required|string",
            "detail" => "nullable|string",
            "type" => "required|exists:types,id",
            "price"=> "required|numeric|gt:0",
            "dose" => "required|string",
            "production_date" => "required|date|before:$request->expiry_date",
            "expiry_date" => "required|date|after:$request->production_date",
            "quantity" => "required|numeric",
            "company_name" => "nullable|string",
        ]) ;

        if($data->fails()){
            return $this->error("الرجاء مراجعة المدخلات, او المنتج موجود مسبقا", $data->errors(), 400);
        }else {
            $data = $data->validated();
            $product = Product::create([
                "id" => $data["id"],
                "name" => $data["name"],
                "detail" => $data["detail"] ?? "",
                "type_id" => $data["type"],
                "dose" => $data["dose"],
                "price" => $data["price"],
                "expiry_date" => $data["expiry_date"],
                "production_date" => $data["production_date"],
                "quantity" => $data["quantity"],
                "created_by_id" => Auth::user()->id,
                "company_name" => $data["company_name"] ?? "",
                "created_at" => date('now'),
                "updated_at" => date('now'),
            ]);

            return $this->success($product, "تم ادخال المنتج بنجاح");
        }
    }

    public function index(Product $id) {
        $product = $id;
        if ($product) {
            return $this->success($product, "success", 200);
        } else {
            return $this->error("error", null, 404);
        }
    }

}
