<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('passes', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->integer('pass_num')->nullable();
      $table->integer('number_sessions')->nullable();
      $table->integer('period_validity')->nullable();
      $table->string('period_validity_unit')->nullable();
      $table->boolean('one_session')->default(false);
      $table->boolean('pass_trimester')->nullable()->default(false);
      $table->boolean('care')->nullable()->default(false);
      $table->timestamps();

      $table->foreignId('pass_category_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('passes');
  }
}
