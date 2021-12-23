<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Type;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       Type::create([
           "name"=>"cream"
    ]);

        Type::create([
            "name"=>"injection"
        ]);

        Type::create([
            "name"=>"tab"
        ]);
    }
}
