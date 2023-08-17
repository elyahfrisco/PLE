<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrimestersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('trimesters', function (Blueprint $table) {
      $table->id();
      $table->integer('num_trimester');
      $table->integer('week_count');
      $table->date('date_start');
      $table->date('date_end');
      $table->timestamps();

      $table->foreignId('season_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('trimesters');
  }
}
