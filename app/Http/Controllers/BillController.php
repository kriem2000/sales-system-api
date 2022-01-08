<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Decimal;
Use App\Models\FragmentedBill;
use Illuminate\Support\Carbon;


class BillController extends Controller
{
    use ApiResponser;
    public function create(Request $request) {
        $data = $request->input("data");
        /*get the total for the invoice*/
        $originalTotal = $this->getInvoiceTotal($data["productsInBasket"]);
        $bill = Bill::create([
            "buyer_name" => $data["buyername"] ?? "",
            "company_name" => $data["companyname"] ,
            "company_address" => $data["companyaddress"] ?? "",
            "delegate_name" => $data["delegatename"] ?? "",
            "sponsor_name" => $data["sponsorname"] ?? "",
            "fragments_number" => $data["fragmentnumber"] ?? 1,
            "payment_period" => $data["Paymentperiod"] ?? "",
            "payment_method_id" => $data["paymentMethod"],
            "bill_status_id" => $data["paymentMethod"] == "1" ? 1 : 2,
            "desc" => $data["desc"] ?? "",
            "original_total" => $originalTotal,
            "applied_discount" => $data["discount"] ?? 0,
            "applied_increase" => $data["applied_increase"] ?? 0,
        ]);
        /*    #step 2 (only if paymentmethod == آجل):
         *    insert into fragmented_bill.
         * */
        if ($bill->payment_method_id == 3) {
            $this->fragmentedBillCases($data["Paymentperiod"], $data["fragmentnumber"], $bill->id);
        }

        $data = ["data" => $data, "bill" => $bill->toArray()];
        return redirect()->action([SaleController::class, 'create'],$data);
    }

    public function getInvoiceTotal($products){
        $total = 0;
        foreach ($products as $product) {
            $total += (float) str_replace(',', '', number_format($product["price"] * $product["salesQuantity"],2));;
        }
        return $total;
    }

    public function fragmentedBillCases($period, $fragments, $billId) {
        switch  ($period) {
            case "daily":
                $nextDay = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextDay,$billId);
                    $nextDay = $nextDay->addDay();
                }
                break;

            case "weekly":
                $nextWeek = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextWeek,$billId);
                    $nextWeek = $nextWeek->addWeek();
                }
                break;

            case "monthly":
                $nextMonth = Carbon::createFromFormat('Y-m-d', date("Y-m-d"));
                for ($i=0 ; $i < $fragments ; $i++) {
                    $this->createFragmentedBill($nextMonth,$billId);
                    $nextMonth = $nextMonth->addMonth();
                }
                break;

            default:
              return  $this->error("لقد حصل خطأ ما","error",400 );
        }
    }

    public function createFragmentedBill($date, $billId){
        $fragmentedBill = FragmentedBill::create([
            "bill_id" => $billId,
            "next_payment_date" => $date,
            "fragment_bill_status_id" => 2,
        ]);

        if ($fragmentedBill) {
            return true;
        } else {
            return false;
        }

    }
}
