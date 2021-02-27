<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_credits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('import_invoice_id')->nullable();
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('package_size')->nullable();
            $table->unsignedInteger('quantity');
            $table->float('purchase_price');
            $table->float('discount')->default(0);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('product_credits');
    }
}
