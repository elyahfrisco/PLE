<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasonsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('seasons', function (Blueprint $table) {
      $table->id();
      $table->date('date_start');
      $table->date('date_end');
      $table->integer('year_start');
      $table->integer('year_end');
      $table->timestamps();

      $table->foreignId('establishment_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('seasons');
  }
}
