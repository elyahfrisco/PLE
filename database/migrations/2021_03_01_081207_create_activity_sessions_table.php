<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySessionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activity_sessions', function (Blueprint $table) {
      $table->id();
      $table->date('date');
      $table->dateTime('time_start');
      $table->dateTime('time_end');
      $table->boolean('shifted')->default(false);
      $table->date('shift_date')->nullable();
      $table->integer('max_effective')->nullable();
      $table->boolean('accomplished')->default(false);
      $table->timestamps();

      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('planning_id');
      $table->foreignId('activity_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('activity_sessions');
  }
}
