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
            'name' => "admin",
            'email' => "admin@gmail.com",
            'password' => Hash::make('123456'),
        ]);

        $user = User::whereName("admin")->firstOrFail();

        $superAdmin = Role::create([
            "name" => "admin"
        ]);

        $seller = Role::create([
            "name" => "seller"
        ]);

        /* general permissions */
        Permission::create([
            "name" => "access"
        ]);

        Permission::create([
            "name" => "index"
        ]);

        /* permissions for managing products */
        Permission::create([
            "name" => "indexProducts"
        ]);

         Permission::create([
            "name" => "createProducts"
        ]);

        Permission::create([
            "name" => "updateProducts"
        ]);

        Permission::create([
            "name" => "deleteProducts"
        ]);

        /* permissions for managing users */
        Permission::create([
            "name" => "indexUsers"
        ]);

        Permission::create([
            "name" => "createUsers"
        ]);

        Permission::create([
            "name" => "updateUsers"
        ]);

        Permission::create([
            "name" => "deleteUsers"
        ]);

        $allPermissions = Permission::all();

        foreach($allPermissions as $permission) {
            $superAdmin->allowTo($permission->name);
        }

        $user->asignRole($superAdmin);

    }
}
