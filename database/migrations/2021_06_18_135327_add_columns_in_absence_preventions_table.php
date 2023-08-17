<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInAbsencePreventionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('absence_preventions', function (Blueprint $table) {
      $table->dateTime('activity_session_time_start');
      $table->foreignId('establishment_id');
      $table->foreignId('activity_id');
      $table->foreignId('pass_id');
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
    Schema::table('absence_preventions', function (Blueprint $table) {
      $table->dropColumn('session_activity_time_start');
      $table->dropColumn('establishment_id');
      $table->dropColumn('activity_id');
      $table->dropColumn('pass_id');
      $table->dropColumn('season_id');
    });
  }
}
