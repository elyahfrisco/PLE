<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecuperationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('recuperations', function (Blueprint $table) {
      $table->id();
      $table->dateTime('date');
      $table->timestamps();

      $table->foreignId('user_id');
      $table->foreignId('season_id');
      $table->foreignId('establishment_id')->references('id')->on('establishments');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('recuperations');
  }
}
