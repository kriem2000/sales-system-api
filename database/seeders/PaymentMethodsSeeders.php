<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::create([
            "name"=> "دفع نقدي",
        ]);
        PaymentMethod::create([
            "name"=> "حِوالة مصرفية",
        ]);
        PaymentMethod::create([
            "name"=> "دفع آجل",
        ]);
    }
}
