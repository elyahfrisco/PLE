<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelaxationCenterEstablishmentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('relaxation_center_establishment', function (Blueprint $table) {
      $table->id();
      $table->foreignId('establishments_id');
      $table->foreignId('relaxation_center_id');
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
    Schema::dropIfExists('relaxation_center_establishment');
  }
}
