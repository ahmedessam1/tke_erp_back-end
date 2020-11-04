<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', trans('validation_standards.titles.max'));
            // EMITTER COULD BE A USER OR EVENT THAT HAPPENS FROM THE SYSTEM OR CRON JOB
            $table->string('emitter', trans('validation_standards.titles.max'));
            $table->text('details');
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
Schema::dropIfExists('events');
    }
}
