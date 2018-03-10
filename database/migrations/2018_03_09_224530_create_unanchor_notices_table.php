<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnanchorNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unanchor_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('character_id');
            $table->bigInteger('structure_id');
            $table->boolean('start_notice')->default(FALSE);
            $table->boolean('finish_notice')->default(FALSE);
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
        Schema::dropIfExists('unanchor_notices');
    }
}
