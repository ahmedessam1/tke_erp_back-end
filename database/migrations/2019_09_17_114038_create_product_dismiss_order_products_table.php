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
