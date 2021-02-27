<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('supplier_address_id');
            $table->unsignedInteger('supplier_contact_id');
            $table->string('national_id', trans('validation_standards.national_id.max'))->nullable();
            $table->double('amount');
            $table->unsignedInteger('payment_type_id');
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('notes', trans('validation_standards.small_descriptions.max'))->nullable();
            $table->date('date');
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
Schema::dropIfExists('supplier_payments');
    }
}
