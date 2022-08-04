<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFragmentedBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fragmented_bill', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("bill_id");
            $table->date("next_payment_date");
            $table->date("payment_date")->nullable();
            $table->decimal("payment_amount");
            $table->unsignedBigInteger("fragment_bill_status_id")->default(2);
            $table->timestamps();

            $table->foreign("fragment_bill_status_id")->references("id")->on("bill_status");
            $table->foreign("bill_id")->references("id")->on("bills");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fragmented_bill');
    }
}
