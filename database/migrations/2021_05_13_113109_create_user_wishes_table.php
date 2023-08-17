<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWishesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_wishes', function (Blueprint $table) {
      $table->id();
      $table->time('time_start');
      $table->time('time_end');
      $table->date('date_start')->nullable();
      $table->string('day')->nullable();
      $table->foreignId('planning_id')->nullable();
      $table->foreignId('activity_id');
      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('user_id');
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
    Schema::dropIfExists('user_wishes');
  }
}
