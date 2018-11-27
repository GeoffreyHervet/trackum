<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOhlcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ohlcs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('coin_id');
            $table->foreign('coin_id')->references('id')->on('coins');
            $table->unsignedInteger('open');
            $table->unsignedInteger('high');
            $table->unsignedInteger('low');
            $table->unsignedInteger('close');
            $table->unsignedBigInteger('volume');
            $table->unsignedBigInteger('marketCap');
            $table->dateTime('openDate');
            $table->dateTime('closeDate');

            $table->index(['coin_id', 'open_date', 'close_date']);
            $table->index(['open_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ohlcs');
    }
}
