<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDismissOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_dismiss_order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_dismiss_order_id');
            $table->unsignedInteger('reason_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            $table->foreign('product_dismiss_order_id')->references('id')->on('product_dismiss_orders');
            $table->foreign('reason_id')->references('id')->on('product_dismiss_reasons');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_dismiss_order_products');
    }
}
