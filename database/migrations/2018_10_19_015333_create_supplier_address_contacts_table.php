<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierAddressContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_address_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('supplier_address_id');
            $table->string('name', trans('validation_standards.names.max'));
            $table->string('phone_number', trans('validation_standards.phone_numbers.max'));
            $table->unsignedInteger('position_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            $table->foreign('supplier_address_id')->references('id')->on('supplier_addresses');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // POSITION RELATIONSHIP
            $table->foreign('position_id')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
Schema::dropIfExists('supplier_address_contacts');
    }
}
