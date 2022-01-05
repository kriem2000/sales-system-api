<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use ApiResponser;

    public function index() {
        $paymentMethods = PaymentMethod::all();

        if (!empty($paymentMethods)) {
            return $this->success($paymentMethods, "success", 200);
        } else {
            return $this->error("error", "error", 500);
        }
    }
}
