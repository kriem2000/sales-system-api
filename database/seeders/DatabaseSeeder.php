<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
/*needed to use the hash and db facades*/
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->insert([
            'name' => "SuperAdmin",
            'email' => "admin@gmail.com",
            'password' => Hash::make('Root123'),
        ]);

        $user = User::whereName("SuperAdmin")->firstOrFail();

        $superAdmin = Role::create([
            "name" => "superAdmin"
        ]);

        $seller = Role::create([
            "name" => "seller"
        ]);

        $visitor = Role::create([
            "name" => "visitor"
        ]);

        $access = Permission::create([
            "name" => "access"
        ]);
        $create = Permission::create([
            "name" => "create"
        ]);
        $insert = Permission::create([
            "name" => "insert"
        ]);
        $update = Permission::create([
            "name" => "update"
        ]);
        $delete = Permission::create([
            "name" => "delete"
        ]);

        $superAdmin->allowTo($access);
        $superAdmin->allowTo($create);
        $superAdmin->allowTo($insert);
        $superAdmin->allowTo($update);
        $superAdmin->allowTo($delete);
        $user->asignRole($superAdmin);

    }
}
