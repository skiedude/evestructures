<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('structure_states', function (Blueprint $table) {
          $table->string('state_timer_start')->nullable()->change();
          $table->string('state_timer_end')->nullable()->change();
          $table->string('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('structure_states', function (Blueprint $table) {
          $table->dropColumn('state');
        });
    }
}
