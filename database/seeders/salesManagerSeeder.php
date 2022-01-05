<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class salesManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salesManager = Role::where("name","sales manager")->first();
        $salesManager->allowTo("access");
        $salesManager->allowTo("indexProducts");
        $salesManager->allowTo("createProducts");
        $salesManager->allowTo("updateProducts");
        $salesManager->allowTo("deleteProducts");
        $salesManager->allowTo("saleProducts");
    }
}

