<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_product', function (Blueprint $table) {
            $table->bigInteger("quantity");
            $table->unsignedBigInteger("sale_id");
            $table->string("product_id");
            $table->timestamps();
            $table->primary(["sale_id","product_id"]);

            $table->foreign("sale_id")
                ->references("id")
                ->on("sales")
                ->onDelete("cascade");

            $table->foreign("product_id")
                ->references("id")
                ->on("products")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_product');
    }
}
