<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecuperationRequestsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('recuperation_requests', function (Blueprint $table) {
      $table->id();

      $table->longText('content');
      $table->dateTime('presence_confirmed_at')->nullable();
      $table->boolean('generate_automatically')->nullable()->default(false);
      $table->foreignId('activity_session_id_to_catch_up');
      $table->foreignId('activity_session_id_for_catch_up');
      $table->foreignId('queue_id');

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
    Schema::dropIfExists('recuperation_requests');
  }
}
