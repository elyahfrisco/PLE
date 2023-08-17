<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelaxationCentersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('relaxation_centers', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('description');
      $table->timestamps();

      $table->foreignId('relaxation_center_category_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('relaxation_centers');
  }
}
