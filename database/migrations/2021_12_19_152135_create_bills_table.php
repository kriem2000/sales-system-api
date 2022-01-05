<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string("buyer_name");
            $table->string("company_name");
            $table->string("company_address")->nullable();
            $table->unsignedBigInteger("payment_method_id");
            $table->unsignedBigInteger("bill_status_id");
            $table->string("desc")->nullable();
            $table->decimal("total");
            $table->string("applied_discount")->nullable();
            $table->timestamps();

            $table->foreign("payment_method_id")->references("id")->on("payment_methods");
            $table->foreign("bill_status_id")->references("id")->on("bill_status");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
