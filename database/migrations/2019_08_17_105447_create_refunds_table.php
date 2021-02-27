<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', trans('validation_standards.names.max'));
            $table->string('number', trans('validation_standards.code.max'));
            $table->unsignedInteger('model_id');
            $table->boolean('tax');
            $table->boolean('approve')->default(0);
            $table->text('notes')->nullable();
            $table->date('date');
            $table->enum('type', ['in', 'out']);
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
        Schema::dropIfExists('refunds');
    }
}
