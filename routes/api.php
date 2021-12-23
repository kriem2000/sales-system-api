<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
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

Route::group(["middleware"=>"auth:sanctum"], function() {
    Route::get('user', function (Request $request) {
        return [
            "userCred" => auth()->user(),
            "permissions" => auth()->user()->permissions()->toArray(),
        ];
    });
    Route::get("signOut",[AuthController::class,"signOut"]);
});

/*super admin routes group*/
Route::middleware(["auth:sanctum","abilities:access,create,update,delete,insert"])->group( function() {

    /*for users*/
    Route::get("roles",[RoleController::class,"index"]);
    Route::post("adduser", [UserController::class,"create"]);
    Route::get("allUsers/{role}", [UserController::class,"indexAll"]);
    Route::post("updateUser/{user}", [UserController::class,"update"]);
    Route::get("deleteUser/{id}",[UserController::class,"delete"]);

    /*for products*/
    Route::post("addProduct",[ProductController::class, "create"]);
});
