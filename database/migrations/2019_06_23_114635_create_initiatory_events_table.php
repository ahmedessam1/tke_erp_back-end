<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitiatoryEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('initiatory_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('initiatory_type_id');
            $table->string('description');
            $table->string('model_type');
            $table->unsignedInteger('model_id');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // TABLES RELATIONSHIP
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            // INITIATORY TYPES RELATIONSHIP
            $table->foreign('initiatory_type_id')->references('id')->on('initiatory_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('initiatory_events');
    }
}
