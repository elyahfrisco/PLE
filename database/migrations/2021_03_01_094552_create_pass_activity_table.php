<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassActivityTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('pass_activity', function (Blueprint $table) {
      $table->integer('number_activity_sessions')->nullable();
      $table->boolean('price_per_person')->default(false);
      $table->timestamps();

      $table->foreignId('pass_id');
      $table->foreignId('activity_id');
      $table->foreignId('group_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('pass_activity');
  }
}
