<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    use ApiResponser;
    public function index() {
        $allTypes = Type::all();
        return $this->success($allTypes, "success", 200);
    }
}
