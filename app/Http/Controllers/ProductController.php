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
            "id" => "required|unique:products",
            "name" => "required|string",
            "detail" => "string",
            "type" => [
                "required" , Rule::in(["cream","injection","tab"])
            ],
            "dose" => "required|string",
            "expiry_date" => "required|date",
            "production_date" => "required|date",
            "quantity" => "required|numeric",
            "company_name" => "string",
            "category_id" => "required|exists:categories,id"
        ]) ;
        if($data->fails()){
            return $this->error("error", $data->errors(), 400);
        }else {
            $data = $data->validated();
            $product = Product::create([
                "id" => $data["id"],
                "name" => $data["name"],
                "detail" => $data["detail"],
                "type" => $data["type"],
                "dose" => $data["dose"],
                "expiry_date" => $data["expiry_date"],
                "production_date" => $data["production_date"],
                "quantity" => $data["quantity"],
                "created_by_id" => Auth::user()->id,
                "company_name" => $data["company_name"],
                "category_id" => $data["category_id"],
                "created_at" => date('now'),
                "updated_at" => date('now'),
            ]);

            return $this->success($product, "product has been created successfully");
        }
    }

}
