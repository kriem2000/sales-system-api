<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail')->nullable();
            $table->unsignedBigInteger("type_id");
            $table->string("dose");
            $table->date("expiry_date");
            $table->date("production_date");
            $table->bigInteger("quantity")->default(0);
            $table->unsignedBigInteger("created_by_id");
            $table->string("company_name")->nullable();
            $table->unsignedBigInteger("category_id");
            $table->timestamps();

            $table->foreign("category_id")->references("id")->on("categories")->onDelete("no action");
            $table->foreign("created_by_id")->references("id")->on("users")->onDelete("no action");;
            $table->foreign("type_id")->references("id")->on("types")->onDelete("no action");;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}