<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanningsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('plannings', function (Blueprint $table) {
      $table->id();
      $table->string('day');
      $table->time('time_start');
      $table->time('time_end');
      $table->dateTime('start_at')->nullable();
      $table->dateTime('end_at')->nullable();
      $table->integer('max_effective')->nullable();
      $table->dateTime('finished_at')->nullable();
      $table->integer('number_activity_sessions')->nullable();
      $table->integer('trimester_num')->nullable();
      $table->boolean('organized')->default(false);
      $table->timestamps();
      $table->softDeletes();

      $table->foreignId('establishment_id');
      $table->foreignId('season_id')->nullable();
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
    Schema::dropIfExists('plannings');
  }
}
