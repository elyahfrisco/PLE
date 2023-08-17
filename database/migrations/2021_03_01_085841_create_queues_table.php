<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('queues', function (Blueprint $table) {
      $table->id();
      $table->date('freeze_date');
      $table->boolean('frozen');
      $table->boolean('presence_confirmed');
      $table->date('presence_confirmation_date');
      $table->integer('priority');
      $table->timestamps();

      $table->foreignId('activity_session_id');
      $table->foreignId('absence_prevention_id');
      $table->foreignId('season_pass_user_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('queues');
  }
}
