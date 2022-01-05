<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class sellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $salesManager = Role::where("name","seller")->first();
        $salesManager->allowTo("access");
        $salesManager->allowTo("indexProducts");
        $salesManager->allowTo("saleProducts");
    }
}
