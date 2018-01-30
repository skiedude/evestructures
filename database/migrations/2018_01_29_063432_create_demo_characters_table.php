<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemoCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_characters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('character_id')->unique();
            $table->integer('corporation_id');
            $table->string('corporation_name');
            $table->string('character_name');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->integer('expires');
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
        Schema::dropIfExists('demo_characters');
    }
}
