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
            $table->string("bill_barcode")->unique();
            $table->string("barcodePNG_path");
            $table->string("buyer_name")->nullable();
            $table->string("company_name");
            $table->string("company_address")->nullable();
            $table->string("company_phone")->nullable();
            $table->string("delegate_name")->nullable();
            $table->string("delegate_phone")->nullable();
            $table->string("sponsor_name")->nullable();
            $table->string("sponsor_phone")->nullable();
            $table->string("fragments_number")->default(1);
            $table->string("payment_period")->nullable();
            $table->unsignedBigInteger("payment_method_id");
            $table->unsignedBigInteger("bill_status_id")->default(1);
            $table->string("desc")->nullable();
            $table->decimal("original_total");
            $table->decimal("total_returned")->default(0);
            $table->decimal("applied_discount")->default(0);
            $table->decimal("applied_increase")->default(0);
            $table->boolean("returned")->default(false);
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
