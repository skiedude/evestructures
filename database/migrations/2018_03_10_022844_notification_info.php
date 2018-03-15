<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotificationInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_info', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id');
          $table->bigInteger('character_id');
          $table->string('fuel_webhook')->nullable();
          $table->string('state_webhook')->nullable();
          $table->string('unanchor_webhook')->nullable();
          $table->string('extraction_webhook')->nullable();
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
      Schema::dropIfExists('notification_info');
    }
}
