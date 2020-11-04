<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerBranchesSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_branches_sellers', function (Blueprint $table) {
            $table->unsignedInteger('seller_id');
            $table->unsignedInteger('customer_branch_id');

            // TABLES RELATIONSHIP
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('customer_branch_id')->references('id')->on('customer_branches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_branches_sellers');
    }
}
