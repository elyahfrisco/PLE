<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstablishmentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('establishments', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('address');
      $table->string('postal_code')->nullable();
      $table->string('phone')->nullable();
      $table->string('email')->nullable();
      $table->time('start_time');
      $table->time('end_time');
      $table->float('latitude')->nullable();
      $table->float('longitude')->nullable();
      $table->boolean('relaxation_center')->default(false);
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
    Schema::dropIfExists('establishments');
  }
}
