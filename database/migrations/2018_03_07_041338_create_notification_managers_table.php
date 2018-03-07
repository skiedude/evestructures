<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_managers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->bigInteger('character_id');
            $table->string('discord_webhook')->nullable();
            $table->string('slack_webhook')->nullable();
            $table->boolean('low_fuel')->default(FALSE);
            $table->boolean('strct_state')->default(FALSE);
            $table->boolean('unanchor')->default(FALSE);
            $table->boolean('extractions')->default(FALSE);
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
        Schema::dropIfExists('notification_managers');
    }
}
