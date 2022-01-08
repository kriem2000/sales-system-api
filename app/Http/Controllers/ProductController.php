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
use App\Http\Controllers\BillController;

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
            "purchase_date" => "required|date|before:$request->expiry_date",
            "expiry_date" => "required|date|after:$request->purchase_date",
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
                "purchase_date" => $data["purchase_date"],
                "quantity" => $data["quantity"],
                "created_by_id" => Auth::user()->id,
                "company_name" => $data["company_name"] ?? "",
                "created_at" => date('now'),
                "updated_at" => date('now'),
            ]);

            return $this->success($product, "تم ادخال المنتج بنجاح");
        }
    }

    public function index($id) {
        $product = Product::where([["id","like",$id],["quantity",">","0"]])->firstOrFail();
        if ($product) {
            return $this->success($product, "success", 200);
        } else {
            return $this->error("error", null, 404);
        }
    }

    public function sale(Request $request) {
        $error = false;
        /* common validation */
        $data = Validator::make($request->all(), [
            "productsInBasket.*.id" => "exists:products,id",
            "productsInBasket.*" => "required",
            "companyname" => "required",
            "companyaddress" => "nullable",
            "buyername" => "nullable",
            "delegatename" => "nullable",
            "sponsorname" => "nullable",
            "paymentMethod" => "exists:payment_methods,id",
            "discount" => "required",
        ]);

        if($request->input("paymentMethod") == '3') {
            if ($request->input("Paymentperiod") == null || $request->input("fragmentnumber") == null) {
                $error = true;
            }
        }

         /*
          * first of all check if the sales Quantity for each product is
          *  less than the original product quantity in the database.
          *  second of all check if the price for each product
          *  has not been manipulated from the front end.
          * */
        foreach ($request->productsInBasket as $product) {
            $currentProduct = Product::find($product["id"]);
            if ($currentProduct->price != $product["price"] ||
                $product["salesQuantity"] > $currentProduct->quantity) {
                $error = true;
                break;
            }
        }

        if ($data->fails() != true && $error != true) {
            $data = $request->all();
           /*
            *  #step 1:
            *   insert into bills :
            * */
            $data = [ "data" => $data ];
            return redirect()->action([BillController::class, 'create'],$data);
        } else {
            return $this->error("error", "الرجاء مراجعة المدخلات", 400);
        }

    }
}
