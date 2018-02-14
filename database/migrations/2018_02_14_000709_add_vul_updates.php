<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVulUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('structure_vuls', function (Blueprint $table) {
            $table->dropColumn('vul_type');
            $table->integer('character_id');
            $table->string('day')->change();
            $table->string('next_day')->nullable();
            $table->integer('next_hour')->nullable();
            $table->string('next_reinforce_apply')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('structure_vuls', function (Blueprint $table) {
          $table->string('vul_type');
          $table->integer('day')->change();
          $table->dropColumn('character_id');
          $table->dropColumn('next_hour');
          $table->dropColumn('next_day');
          $table->dropColumn('next_reinforce_apply');
        });
    }
}
