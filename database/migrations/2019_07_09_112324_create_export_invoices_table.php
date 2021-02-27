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
            $table->string('number', trans('validation_standards.code.max'));
            $table->date('date');
            $table->boolean('tax');
            $table->float('discount')->default(0);
            $table->boolean('approve')->default(0);
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
        Schema::dropIfExists('export_invoices');
    }
}
