<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('money_courier_id');
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

            // TABLES RELATIONSHIP
            // USERS
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // SUPPLIERS
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('money_courier_id')->references('id')->on('users');
            // PAYMENT TYPE
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_payments');
    }
}
