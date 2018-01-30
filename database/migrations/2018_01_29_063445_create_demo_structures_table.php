<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemoStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('corporation_id');
            $table->bigInteger('structure_id');
            $table->string('structure_name');
            $table->integer('type_id');
            $table->string('type_name');
            $table->bigInteger('system_id');
            $table->string('system_name');
            $table->integer('profile_id');
            $table->string('fuel_expires')->nullable();
            $table->string('unanchors_at')->nullable();
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
        Schema::dropIfExists('demo_structures');
    }
}
