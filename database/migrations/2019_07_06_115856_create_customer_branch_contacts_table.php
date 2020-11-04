<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerBranchContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_branch_contacts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('customer_branch_id');
            $table->string('name', trans('validation_standards.names.max'));
            $table->string('phone_number', trans('validation_standards.phone_numbers.max'));
            $table->unsignedInteger('position_id');

            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            $table->foreign('customer_branch_id')->references('id')->on('customer_branches');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_branch_contacts');
    }
}
