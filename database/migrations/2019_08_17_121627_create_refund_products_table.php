<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('refund_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('quantity');
            $table->float('price');
            $table->boolean('valid')->default(1);
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            $table->foreign('refund_id')->references('id')->on('refunds');
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
        Schema::dropIfExists('refund_products');
    }
}
