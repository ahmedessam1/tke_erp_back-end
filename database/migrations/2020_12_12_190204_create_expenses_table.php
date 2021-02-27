<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->nullable(); // RELATIONSHIP
            $table->unsignedInteger('user_id')->nullable(); // RELATIONSHIP
            $table->unsignedInteger('expense_type_id'); // RELATIONSHIP
            $table->unsignedInteger('payment_type_id'); // RELATIONSHIP
            $table->string('title', trans('validation_standards.titles.max'))->nullable();
            $table->date('date');
            $table->double('amount');
            $table->string('national_id', trans('validation_standards.national_id.max'))->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('notes', trans('validation_standards.descriptions.max'))->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
