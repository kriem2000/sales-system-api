<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
/*needed to use the hash and db facades*/
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CategoriesSeedrs extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        Category::create([
            "name" => "medicine",
            "desc" => "this is the first category",
            "created_at" => date('now'),
            "updated_at" => date('now')
        ]);
    }
}
