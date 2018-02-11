<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFuelDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demo_structures', function (Blueprint $table) {
          $table->integer('fuel_days_left')->nullable();
          $table->string('fuel_time_left')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demo_structures', function (Blueprint $table) {
          $table->dropColumn('fuel_days_left');
          $table->dropColumn('fuel_time_left');
        });
    }
}
