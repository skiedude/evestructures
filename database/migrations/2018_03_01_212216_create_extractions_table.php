<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extractions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('structure_id');
            $table->bigInteger('moon_id');
            $table->string('moon_name');
            $table->timestamp('extraction_start_time')->useCurrent();
            $table->timestamp('chunk_arrival_time')->useCurrent();
            $table->timestamp('natural_decay_time')->useCurrent();
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
        Schema::dropIfExists('extractions');
    }
}
