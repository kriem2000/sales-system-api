<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExportedBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exported_bills', function (Blueprint $table) {
            $table->id();
            $table->string("bill_name")->unique();
            $table->string("exporter_name");
            $table->string("exporter_phone")->nullable();
            $table->date("bill_date")->nullable();
            $table->unsignedBigInteger("created_by");
            $table->timestamps();

            $table->foreign("created_by")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exported_bills');
    }
}
