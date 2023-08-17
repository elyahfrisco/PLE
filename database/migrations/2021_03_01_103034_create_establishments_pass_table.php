<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstablishmentsPassTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('establishments_pass', function (Blueprint $table) {
      $table->timestamps();
      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('pass_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('establishments_pass');
  }
}
