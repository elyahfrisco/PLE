<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassRelaxationCenterTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('pass_relaxation_center', function (Blueprint $table) {
      $table->integer('number_access');
      $table->timestamps();

      $table->unsignedBigInteger('pass_id')->foreign()->references('id')->on('passes');
      $table->unsignedBigInteger('relaxation_center_id')->foreign()->references('id')->on('relaxation_centers');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('pass_relaxation_center');
  }
}
