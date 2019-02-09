<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPingHereToNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_info', function (Blueprint $table) {
          $table->boolean('fuel_ping_here')->default(FALSE);
          $table->boolean('state_ping_here')->default(FALSE);
          $table->boolean('anchor_ping_here')->default(FALSE);
          $table->boolean('extraction_ping_here')->default(FALSE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_info', function (Blueprint $table) {
          $table->dropColumn('fuel_ping_here');
          $table->dropColumn('state_ping_here');
          $table->dropColumn('anchor_ping_here');
          $table->dropColumn('extraction_ping_here');
        });
    }
}
