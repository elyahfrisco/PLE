<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodeSubscriptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('periode_subscriptions', function (Blueprint $table) {
      $table->id();
      $table->date('start_at');
      $table->date('end_at');
      $table->longText('description')->nullable();
      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('season_id');
      $table->foreignId('pass_id')->nullable();
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
    Schema::dropIfExists('periode_subscriptions');
  }
}
