<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', trans('validation_standards.names.max'))->unique();
            $table->string('barcode', trans('validation_standards.barcode.size'))->nullable();
            $table->unsignedInteger('local_code_id');
            $table->unsignedInteger('category_id');
            $table->text('description')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            // USERS
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // CATEGORIES
            $table->foreign('category_id')->references('id')->on('categories');
            // LOCAL_CODE
            $table->foreign('local_code_id')->references('id')->on('local_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
Schema::dropIfExists('products');
    }
}
