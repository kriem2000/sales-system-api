<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;


class RoleController extends Controller
{
    use ApiResponser;

    public function index() {
        $roles = Role::all()->pluck("name")->toArray();
        return $this->success($roles, 'data fetched successfully',200);
    }
}
