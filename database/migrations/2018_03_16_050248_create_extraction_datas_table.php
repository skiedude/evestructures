<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtractionDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extraction_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('structure_id');
            $table->bigInteger('value')->default(0);
            $table->string('ores')->default('Ore');
            $table->string('fracture_pref')->default('auto_fracture');
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
        Schema::dropIfExists('extraction_datas');
    }
}
