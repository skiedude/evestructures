<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructureStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_states', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('structure_id');
            $table->bigInteger('character_id');
            $table->string('state_timer_start');
            $table->string('state_timer_end');
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
        Schema::dropIfExists('structure_states');
    }
}
