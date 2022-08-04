<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\SaleProducts;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\BillController;
use App\Models\ExportedBillProduct;

class ProductController extends Controller
{

    use ApiResponser;
    public function create(Request $request) {
        $data = Validator::make($request->all(), [
            "id" => "required|unique:products,id",
            "bill_id" => "required|exists:exported_bills,id",
            "name" => "required|string",
            "detail" => "nullable|string",
            "type" => "required|exists:types,id",
            "salesPrice"=> "required|numeric|gt:0",
            "purchasePrice"=> "required|numeric|gt:0",
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
            /*insert into products*/
            $product = Product::create([
                "id" => $data["id"],
                "name" => $data["name"],
                "detail" => $data["detail"] ?? "",
                "type_id" => $data["type"],
                "dose" => $data["dose"],
                "sale_price" => $data["salesPrice"],
                "purchase_price" => $data["purchasePrice"],
                "expiry_date" => $data["expiry_date"],
                "purchase_date" => $data["purchase_date"],
                "quantity" => $data["quantity"],
                "created_by_id" => Auth::user()->id,
                "company_name" => $data["company_name"] ?? "",
                "created_at" => date('now'),
                "updated_at" => date('now'),
            ]);
            /*insert into exported_bills_products*/
            ExportedBillProduct::create([
                "exported_bill_id" => $data["bill_id"],
                "product_id" => $data["id"],
                "quantity" => $data["quantity"],
                "purchase_price" => $data["purchasePrice"],
                "sales_price" => $data["salesPrice"],
                "created_by" => auth()->user()->id,
            ]);

            return $this->success($product, "تم ادخال المنتج بنجاح");
        }
    }

    public function update(Request $request) {
        $data = Validator::make($request->all(), [
            "id" => "required|exists:products,id",
            "bill_id" => "nullable|exists:exported_bills,id",
            "name" => "required|string",
            "detail" => "nullable|string",
            "type" => "required|exists:types,id",
            "salesPrice"=> "required|numeric|gt:0",
            "purchasePrice"=> "required|numeric|gt:0",
            "dose" => "required|string",
            "purchase_date" => "required|date|before:$request->expiry_date",
            "expiry_date" => "required|date|after:$request->purchase_date",
            "quantity" => "required|numeric",
            "company_name" => "nullable|string",
        ]);
        if($data->fails()) {
            return $this->error("error", $data->errors(), 400);
        }else {
            $data = $data->validated();
            if ($data["bill_id"] != null) {
                /*insert into exported bill if the bill has been changed*/
                ExportedBillProduct::create([
                    "exported_bill_id" => $data["bill_id"],
                    "product_id" => $data["id"],
                    "quantity" => $data["quantity"],
                    "purchase_price" => $data["purchasePrice"],
                    "sales_price" => $data["salesPrice"],
                    "created_by" => auth()->user()->id,
                ]);
            }
            /*update the product*/
            Product::find($data["id"])->update([
                "name" => $data["name"],
                "detail" => $data["detail"] ?? "",
                "type_id" => $data["type"],
                "dose" => $data["dose"],
                "sale_price" => $data["salesPrice"],
                "purchase_price" => $data["purchasePrice"],
                "expiry_date" => $data["expiry_date"],
                "purchase_date" => $data["purchase_date"],
                "quantity" => $data["quantity"],
                "company_name" => $data["company_name"] ?? "",
            ]);
            return $this->success("ok", "success", 200);
        }
    }

    public function index($id) {
        $product = Product::where([["id","like",$id],["quantity",">","0"]])->count();
        if (!empty($product)) {
            $product = Product::where([["id","like",$id],["quantity",">","0"]])->firstOrFail();
            return $this->success($product, "success", 200);
        } else {
            $product = Product::where([["name","like",$id],["quantity",">","0"]])->count() ;
            if ($product > 0) {
                $product = Product::where([["name","like",$id],["quantity",">","0"]])->firstOrFail() ;
                return $this->success($product, "success", 200);
            } else {
                return $this->error("error", null, 404);
            }
        }
    }

    public function indexAll($filterBy = "created_at", $productName = "") {
        $products = Product::with("user")
            ->orderByDesc($filterBy)
            ->where("name", "like", "%".$productName."%")
            ->get();
        return $this->success($products,"success",200);
    }

    public function sale(Request $request) {
        $error = false;
        /* common validation */
        $data = Validator::make($request->all(), [
            "productsInBasket.*.id" => "exists:products,id",
            "productsInBasket.*" => "required",
            "companyname" => "required",
            "companyaddress" => "nullable",
            "companyPhone" => "nullable",
            "delegatePhone" => "nullable",
            "billDesc" => "nullable|max:255",
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
            if ($currentProduct->sale_price != $product["sale_price"] ||
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

    public function return(Request $request) {
        $error = false;
        foreach ($request->all() as $data) {
            $current = SaleProducts::where([
                ["product_id" ,"=", $data["product"]],
                ["sale_id" ,"=", $data["sale"]],
                ["quantity" ,">=", $data["quantity"]]
            ]) ->count();
            if($current != 1) {
                $error = true;
                break;
            }
        }
        if ($error) {
            return $this->error("error", "error", 400);
        }else {
            $data = ["data" => $request->all()];
            return redirect()->action([BillController::class, 'return'],$data);
        }
    }

    /*need to be reduced !!! and all other statistics*/
    public function productsStatics() {
        $ProductsStatistics = [];

        $bills = Bill::all("original_total", "applied_discount", "total_returned");
        $allProducts = ExportedBillProduct::all("purchase_price", "sales_price", "quantity");
        $allProductsInventory = Product::all("purchase_price", "sale_price", "quantity");
        $allSales = SaleProducts::all("product_id","quantity");

        $totalPurchase =  $allProducts->map(function( $val, $key) {
            return str_replace(",", "", number_format($val["purchase_price"] * $val["quantity"], 2));
        })->sum();
        $totalPurchaseInventory = $allProductsInventory->map(function( $val, $key) {
            return str_replace(",", "", number_format($val["purchase_price"] * $val["quantity"], 2));
        })->sum();
        $totalSalesInventory = $allProductsInventory->map(function( $val, $key) {
            return str_replace(",", "", number_format($val["sale_price"] * $val["quantity"], 2));
        })->sum();

        /*to get total gained in all times*/
        /*get the total bill with the purchase_price in place of sale_price*/
        $totalOriginalSales = $allSales->map(function($item, $key) {
            return Product::find($item['product_id'])->purchase_price * $item["quantity"];
        })->sum();
        $totalSales =  ($bills->map(function($item,$key) {
                return  ($item["original_total"] - ($item["original_total"] * $item["applied_discount"])) - $item["total_returned"];
            })
            ->sum());
        $totalGained = $totalSales - $totalOriginalSales;
        /**********/
        $totalGainedInventory = ($allProductsInventory->map(function( $val, $key) {
            return str_replace(",", "", number_format($val["sale_price"] * $val["quantity"], 2));
        })->sum()) - ($allProductsInventory->map(function( $val, $key) {
            return str_replace(",", "", number_format($val["purchase_price"] * $val["quantity"], 2));
        })->sum());

        $ProductsStatistics["totalPurchase"] = $totalPurchase;
        $ProductsStatistics["totalPurchaseInventory"] = $totalPurchaseInventory;
        $ProductsStatistics["totalSalesInventory"] = $totalSalesInventory;
        $ProductsStatistics["totalGained"] = $totalGained;
        $ProductsStatistics["totalGainedInventory"] = $totalGainedInventory;

        return $this->success($ProductsStatistics, "ok", 200);
    }

    public function search($codeOrName) {
        $products = Product::where("id", "like", "%".$codeOrName."%")
                            ->orWhere("name", "like", "%".$codeOrName."%")
                            ->get();
        return $this->success($products, "ok", 200);
    }
}
