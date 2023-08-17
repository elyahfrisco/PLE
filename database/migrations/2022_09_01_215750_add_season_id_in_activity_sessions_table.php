<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSeasonIdInActivitySessionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->foreignId('season_id')->after('establishment_id');
    });

    DB::statement(
      "UPDATE activity_sessions, plannings
            SET activity_sessions.season_id = plannings.season_id
            WHERE activity_sessions.planning_id = plannings.id"
    );

    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->foreign('season_id')->references('id')->on('seasons')->cascadeOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
  }
}
