<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_branch_id');
            $table->unsignedInteger('seller_id');
            $table->string('name', trans('validation_standards.names.max'));
            $table->unsignedBigInteger('number')->unique();
            $table->date('date');
            $table->boolean('tax');
            $table->float('discount')->default(0);
            $table->boolean('approve')->default(0);
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            // USERS
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // SUPPLIER
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
        Schema::dropIfExists('export_invoices');
    }
}
