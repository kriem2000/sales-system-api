<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportedBillsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exported_bills_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("exported_bill_id");
            $table->string("product_id");
            $table->integer("quantity")->nullable();
            $table->decimal("purchase_price")->nullable();
            $table->decimal("sales_price")->nullable();
            $table->unsignedBigInteger("created_by");
            $table->timestamps();

            $table->foreign("created_by")->references("id")->on("users");
            $table->foreign("exported_bill_id")->references("id")->on("exported_bills");
            $table->foreign("product_id")->references("id")->on("products");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exported_bills_products');
    }
}
