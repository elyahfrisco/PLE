<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasonPassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season_pass', function (Blueprint $table) {
            $table->timestamps();
            $table->foreignId('season_id');
            $table->foreignId('establishment_id')->references('id')->on('establishments');
            $table->foreignId('pass_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('season_pass');
    }
}
