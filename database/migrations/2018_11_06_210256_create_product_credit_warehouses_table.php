<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateProductCreditWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_credit_warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_credit_id');
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            // USERS
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // PRODUCT CREDITS
            $table->foreign('product_credit_id')->references('id')->on('product_credits');
            // WAREHOUSES
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
Schema::dropIfExists('product_credit_warehouses');
    }
}
