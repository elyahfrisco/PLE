<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsencePreventionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('absence_preventions', function (Blueprint $table) {
      $table->id();
      $table->date('date');
      $table->longText('reason');
      $table->timestamps();

      $table->foreignId('activity_session_id');
      $table->foreignId('user_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('absence_preventions');
  }
}
