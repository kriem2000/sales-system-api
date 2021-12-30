<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponser;
    public function index() {
        $allCategories = Category::all();
        return $this->success($allCategories, "success", 200);
    }
}
