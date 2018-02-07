<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_notices', function (Blueprint $table) {
            $table->increments('id');
						$table->bigInteger('user_id');
						$table->bigInteger('character_id');
						$table->bigInteger('structure_id');
						$table->boolean('seven_day')->default(FALSE);
						$table->boolean('twentyfour_hour')->default(FALSE);
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
        Schema::dropIfExists('fuel_notices');
    }
}
