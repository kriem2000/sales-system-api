<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleProducts;
use App\Models\Product;

class SaleController extends Controller
{
    use ApiResponser;
    public function create(Request $request) {
        $bill = $request->input("bill");
        $data = $request->input("data");

        /* step 3 : insert into sales.
         * */
        $sale = Sale::create([
            "bill_id" => $bill["id"],
            "sold_by_id" => auth()->user()->id,
        ]);

        /* step 4 : insert into sales_products.
        */
        foreach ($data["productsInBasket"] as $product) {
            SaleProducts::create([
                "quantity" => $product["salesQuantity"],
                "sale_id" => $sale->id,
                "product_id" => $product["id"]
            ]);
            /*
             * step 5 (or can be the first step):
             * update products quantity for each product in basket.
             * */
            Product::find($product["id"])->decrement("quantity", $product["salesQuantity"]);
        }

        $data = ["requested_info" => $data, "bill" => $bill, "sale" => $sale ];
        return $this->success($data, 'from sales controller', 200);
    }
}
