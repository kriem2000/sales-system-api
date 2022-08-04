<?php
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\ReturnProducts;
use App\Models\ReturnTable;
use App\Models\SaleProducts;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Decimal;
Use App\Models\FragmentedBill;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    use ApiResponser;
    public function create(Request $request) {
        $data = $request->input("data");
        /*get the total for the invoice*/
        $originalTotal = $this->getInvoiceTotal($data["productsInBasket"]);
        /*get the invoice discount if exists otherwise 0*/
        $discount = $data["discount"] ?? 0;

        /*generate the barcode number and make barcode PNG image and store it in the storage*/
        $bill_barcode =  random_int(100,100000000000);
        $bill_barcodeDB = $bill_barcode;
        while (Bill::where("bill_barcode", "like", $bill_barcode)->count() >= 1) {
            $bill_barcode = random_int(100,100000000000);
            $bill_barcodeDB = $bill_barcode;
        }
        $generatorJPG = new BarcodeGeneratorPNG();
        Storage::disk('public')
            ->put('barcodePNG/'.$bill_barcode.".png", $generatorJPG
                ->getBarcode($bill_barcode, $generatorJPG::TYPE_CODE_128), 'public');
        $full_path = env("APP_URL")."/"."storage/barcodePNG/".$bill_barcode.".png";
        /* get the image and store it in a variable to be used after */
        /**************/

        $bill = Bill::create([
            "bill_barcode" => $bill_barcodeDB . "", // concat for somereseaon it saves onother negative number with out this concat
            "barcodePNG_path" => $full_path,
            "buyer_name" => $data["buyername"] ?? "",
            "company_name" => $data["companyname"] ,
            "company_address" => $data["companyaddress"] ?? "",
            "company_phone" => $data["companyPhone"] ?? "",
            "delegate_name" => $data["delegatename"] ?? "",
            "delegate_phone" => $data["delegatePhone"] ?? "" ,
             "sponsor_name" => $data["sponsorname"] ?? "",
            "fragments_number" => $data["fragmentnumber"] ?? 1,
            "payment_period" => $data["Paymentperiod"] ?? "",
            "payment_method_id" => $data["paymentMethod"],
            "bill_status_id" => $data["paymentMethod"] == "1" ? 1 : 2,
            "desc" => $data["billDesc"] ?? "",
            "original_total" => $originalTotal,
            "applied_discount" => $data["discount"] ?? 0,
            "applied_increase" => $data["applied_increase"] ?? 0,
        ]);
        /*    #step 2 (only if paymentmethod == آجل):
         *    insert into fragmented_bill.
         * */
        if ($bill->payment_method_id == 3) {
            $this->fragmentedBillCases($data["Paymentperiod"], $data["fragmentnumber"], $bill->id, $originalTotal, $discount);
        }

        $data = ["data" => $data, "bill" => $bill->toArray()];
        return redirect()->action([SaleController::class, 'create'],$data);
    }

    public function getInvoiceTotal($products){
        $total = 0;
        foreach ($products as $product) {
            $total += (float) str_replace(',', '',number_format($product["sale_price"] * $product["salesQuantity"],2));
        }
        return $total;
    }

    public function fragmentedBillCases($period, $fragments, $billId, $originalTotal, $discount) {
        $paymentAmount = (float) str_replace(',', '', number_format(($originalTotal - ($originalTotal * $discount)) / $fragments, 2));
        switch  ($period) {
            case "daily":
                $nextDay = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextDay,$billId, $paymentAmount);
                    $nextDay = $nextDay->addDay();
                }
                break;

            case "weekly":
                $nextWeek = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextWeek,$billId, $paymentAmount);
                    $nextWeek = $nextWeek->addWeek();
                }
                break;

            case "monthly":
                $nextMonth = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextMonth,$billId, $paymentAmount);
                    $nextMonth = $nextMonth->addMonth();
                }
                break;

            default:
              return  $this->error("لقد حصل خطأ ما","error",400 );
        }
    }

    public function createFragmentedBill($date, $billId, $paymentAmount){
        $fragmentedBill = FragmentedBill::create([
            "bill_id" => $billId,
            "next_payment_date" => $date,
            "payment_amount" => $paymentAmount,
            "fragment_bill_status_id" => 2,
        ]);

        if ($fragmentedBill) {
            return true;
        } else {
            return false;
        }

    }

    public function index($from = null, $to = null, $paymentStatus = "%", $paymentMethod = "%") {
        $from == "empty" ? ($from = DB::table("bills")->oldest()->first()->created_at) : "";
        $to == "empty" ? ($to = DB::table("bills")->latest()->first()->created_at) : "" ;
        $paymentStatus == "empty" ? ($paymentStatus = "%") : "";
        $paymentMethod == "empty" ? ($paymentMethod = "%") : "";

        $bills = Bill::with(["sale" => function($query) {
            $query->with("products");
        }, "fragmented_bill", "billStatus", "paymentMethod", "returns" => function($query) {
            $query->with("product");
        }])->whereBetween("created_at", [$from, $to])->orderByDesc("created_at")
            ->where([
                ["bill_status_id", "like", $paymentStatus],
                ["payment_method_id", "like", $paymentMethod]
                ])
           ->paginate(6);

        foreach ($bills as $bill) {
            $bill["file"] = base64_encode(Storage::disk('public')->get("/barcodePNG/".$bill["bill_barcode"].".png"));
        }

        return $this->success($bills, $from." / ".$to." / ".$paymentStatus, 200);
    }

    public function return(Request $request) {
        $data = $request->input("data");
        $bill_id = $data[0]['bill'];
        $bill = Bill::find($bill_id);
        $total_returned = 0;
        foreach($data as $products) {
            $product = Product::find($products["product"]);
            $total_returned += (float) str_replace(',', '', number_format(($product->sale_price - ($product->sale_price * $bill->applied_discount)) * $products["quantity"], 2));
        }
        $bill->returned = true;
        $bill->total_returned = $total_returned + $bill->total_returned;
        $bill->save();

        /*only if the bill is fragmented*/
        if ($bill->payment_method_id == 3) {
            $payment_amount = (float) str_replace(",", "",
                number_format(
                    ($bill->original_total - ($bill->original_total * $bill->applied_discount) - $total_returned) / $bill->fragments_number, 2)
            );
            FragmentedBill::where("bill_id","=",$bill->id)->update(["payment_amount" => $payment_amount]);
        }

        /*decrease the quantity of each product in sales products*/
        foreach ($data as $products) {
            SaleProducts::where([["sale_id", "=", $products["sale"]],["product_id", "=", $products["product"]]])
                ->decrement("quantity", $products["quantity"]);
            Product::where("id","=",$products["product"])->increment("quantity", $products["quantity"]);
        }

        /*insert into returns products*/
        foreach ($data as $products) {
            ReturnProducts::create([
                "product_id" => $products["product"],
                "bill_id" => $bill->id,
                "returned_by_id" => auth()->user()->id,
                "quantity" => $products["quantity"],
            ]);
        }

        return $this->success($total_returned, "from bill controller", 200);
    }

    public function changeStatus(Bill $id) {
        $billInPending = FragmentedBill::where([["bill_id", $id->id],["fragment_bill_status_id", "!=", 1]])->count();
        if($billInPending == 0) {
            $id->bill_status_id = 1;
            $id->save();
            return $this->success("success", "success", 200);
        } else {
            return $this->error("توجد فواتير آجله لم يتم دفعها", "error", 400);
        }
    }
}
