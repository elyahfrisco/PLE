<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('prices', function (Blueprint $table) {
      $table->id();
      $table->float('price');
      $table->float('reduced_price')->nullable();
      $table->timestamps();

      $table->foreignId('trimester_id')->nullable();
      $table->foreignId('activity_id')->nullable();
      $table->foreignId('pass_id')->nullable();
      $table->foreignId('establishment_id');
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
    Schema::dropIfExists('prices');
  }
}
