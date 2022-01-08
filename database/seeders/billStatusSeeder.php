<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BillStatus;

class billStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BillStatus::create([
            "name" => "تم الدفع",
        ]);

        BillStatus::create([
            "name" => "في الانتظار",
        ]);
    }
}
