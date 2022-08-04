<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class accountantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accountant = Role::where("name","accountant")->first();
        $accountant->allowTo("indexProducts");
        $accountant->allowTo("access");
    }
}
