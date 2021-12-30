<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('auth/signIn', [AuthController::class, 'signIn']);

Route::group(["middleware"=>"auth:sanctum","abilities:access"], function() {
    Route::get('user', function (Request $request) {
        return [
            "userCred" => auth()->user(),
            "permissions" => auth()->user()->permissions()->toArray(),
        ];
    });
    Route::get("signOut",[AuthController::class,"signOut"]);
    Route::get("allTypes",[TypeController::class,"index"])->middleware("abilities:indexProducts,createProducts");
});



/*routes group for user management*/
Route::middleware(["auth:sanctum","abilities:access,index,indexUsers,createUsers,updateUsers,deleteUsers"])
->group( function() {

    Route::get("roles",[RoleController::class,"index"]);
    Route::post("adduser", [UserController::class,"create"]);
    Route::get("allUsers/{role}", [UserController::class,"indexAll"]);
    Route::post("updateUser/{user}", [UserController::class,"update"]);
    Route::get("deleteUser/{id}",[UserController::class,"delete"]);

});
    /********/


/*routes group for product management*/
Route::middleware(["auth:sanctum","abilities:access,index,indexProducts"])->group(function () {
    Route::post("addProduct",[ProductController::class, "create"])->middleware("abilities:createProducts");
    Route::get("product/{id}",[ProductController::class, "index"]);
});
   /********/

