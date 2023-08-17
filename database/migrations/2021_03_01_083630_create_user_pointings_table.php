<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPointingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pointings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_recuperation');
            $table->timestamps();

            $table->foreignId('user_id');
            $table->foreignId('activity_session_id');
            $table->foreignId('queue_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_pointings');
    }
}
