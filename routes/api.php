<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExportedBillController;
use App\Http\Controllers\FragmentedBillController;
use App\Http\Controllers\ReturnProductsController;

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
    Route::get('user', [UserController::class, "info"]);
    Route::get("signOut",[AuthController::class,"signOut"]);
    Route::get("allTypes",[TypeController::class,"index"])
        ->middleware("abilities:indexProducts,createProducts");
});


/*routes group for user management*/
Route::middleware(["auth:sanctum","abilities:access,indexUsers,createUsers,updateUsers,deleteUsers"])
->group( function() {

    Route::get("roles",[RoleController::class,"index"]);
    Route::post("adduser", [UserController::class,"create"]);
    Route::get("allUsers/{role?}", [UserController::class,"indexAll"]);
    Route::post("updateUser/{user}", [UserController::class,"update"]);
    Route::get("deleteUser/{id}",[UserController::class,"delete"]);

});
    /********/


/*routes group for product management*/
Route::middleware(["auth:sanctum","abilities:access,indexProducts"])->group(function () {
    /*routes for the exported bill*/
    Route::post("addExportedBill", [ExportedBillController::class, "create"])
        ->middleware("abilities:createProducts");
    Route::get("allExportedBills", [ExportedBillController::class, "index"])
        ->middleware("abilities:createProducts");

    /*routes for product crud*/
    Route::post("addProduct",[ProductController::class, "create"])
        ->middleware("abilities:createProducts");
    Route::post("updateProduct", [ProductController::class, "update"])
        ->middleware("abilities:createProducts");
    Route::get("product/{id}",[ProductController::class, "index"])
        ->middleware("abilities:saleProducts");
    Route::get("allProducts/{filterBy?}/{productName?}", [ProductController::class, "indexAll"])
        ->middleware("abilities:saleProducts,createProducts,updateProducts,deleteProducts");
    Route::get("paymentMethods", [PaymentMethodController::class,"index"])
        ->middleware("abilities:saleProducts");
    Route::get("quickSearch/{codeOrName?}", [ProductController::class, "Search"])
        ->middleware("abilities:saleProducts");

    /* routes for sale and generate a single bill */
    Route::post("saleProduct", [ProductController::class, "sale"])
        ->middleware("abilities:saleProducts");
    Route::get("createBill", [BillController::class, "create"])
        ->middleware("abilities:saleProducts");
    Route::get("createSale",[SaleController::class, "create"])
        ->middleware("abilities:saleProducts");
    /*******/

    /* routes for fetching bills */
    Route::get("allBills/{from?}/{to?}/{paymentStatus?}/{paymentMethod?}", [BillController::class, "index"]);
    /*******/

    /* routes for return a bill */
    Route::post("returnProduct", [ProductController::class,"return"])
        ->middleware("abilities:saleProducts");
    Route::get("returnBill", [BillController::class, "return"])
        ->middleware("abilities:saleProducts");
    /*******/

    /* changing bill status */
    Route::get("changeFragmentBillStatus/{id}", [FragmentedBillController::class, "changeStatus"])
        ->middleware("abilities:saleProducts");
    Route::get("changeBillStatus/{id}", [BillController::class, "changeStatus"])
        ->middleware("abilities:saleProducts");
});
   /********/

/*routes for statistics*/
Route::group(["middleware" => "auth:sanctum","abilities:access"] ,function() {
    /*sales statistics*/
    Route::get("sales-Statistics", [SaleController::class, "salesStatistics"]);
       // ->middleware("abilities:viewStatistics");
    Route::get("returns-Statistics", [ReturnProductsController::class, "returnsStatistics"]);
    Route::get("fragmentPayments-Statistics", [FragmentedBillController::class, "fragmentPaymentsStatistics"]);
    Route::get("paymentMethods-Statistics", [PaymentMethodController::class, "paymentMethodsStatistics"]);
    Route::get("products-Statistics", [ProductController::class, "productsStatics"]);
});
