<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\FragmentedBill;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleProducts;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

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

        $data = ["requested_info" => $data, "bill" => $bill, "sale" => $sale];
        $bill = Bill::with(["sale" => function($query) {
            $query->with("products");
        }, "fragmented_bill", "paymentMethod"])
            ->where("bill_barcode", "like", $bill["bill_barcode"])->get()->toArray();

        $bill[0]["file"] = base64_encode(Storage::disk('public')->get("/barcodePNG/".$bill[0]["bill_barcode"].".png"));

        return $this->success($bill, 'from sales controller', 200);
    }

    public function salesStatistics() {
        $allSalesStatistics = [];
        $now = Carbon::now();

        $allSales = Bill::all();

        $dailySales = Bill::whereDate("created_at", Carbon::today()->format("Y-m-d"))->get();

        $weeklySales = Bill::whereBetween(
            "created_at",
            [$now->startOfWeek()->format('Y-m-d'),  $now->endOfWeek()->format('Y-m-d')]
        )
            ->get();

        $monthlySales = Bill::whereDate(
            "created_at","like" ,
            Carbon::now()->format('Y')."-".Carbon::now()->format('m')."%"
            )
            ->get();

        $annualySales = Bill::whereDate("created_at", "like" ,Carbon::now()->format('Y')."%")->get();

        $allSalesStatistics["dialySales"] = $dailySales;
        $allSalesStatistics["allSales"] = $allSales;
        $allSalesStatistics["weeklySales"] = $weeklySales;
        $allSalesStatistics["monthlySales"] = $monthlySales;
        $allSalesStatistics["annualySales"] = $annualySales;

        foreach ($allSalesStatistics as $key => $salesStatistic) {
            $allSalesStatistics[$key] = $salesStatistic->map(function($item,$key) {
                return  ($item["original_total"] - ($item["original_total"] * $item["applied_discount"])) - $item["total_returned"];
            })
                ->sum();
        }
        return $this->success($allSalesStatistics, "success", 200);
    }

}
