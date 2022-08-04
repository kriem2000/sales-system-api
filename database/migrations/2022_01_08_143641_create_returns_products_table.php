<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returns_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("bill_id");
            $table->unsignedBigInteger("returned_by_id");
            $table->string("product_id");
            $table->integer("quantity");
            $table->timestamps();

            $table->foreign("bill_id")->references("id")->on("bills");
            $table->foreign("product_id")
                ->references("id")
                ->on("products")
                ->onDelete("cascade");
            $table->foreign("returned_by_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('returns_products');
    }
}
