<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexInActivitySessionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('activity_sessions')
        ->leftJoin('establishments', 'activity_sessions.establishment_id', 'establishments.id')
        ->leftJoin('plannings', 'activity_sessions.planning_id', 'plannings.id')
        ->leftJoin('activities', 'activity_sessions.activity_id', 'activities.id')
        ->whereNull('establishments.id')
        ->orWhereNull('plannings.id')
        ->orWhereNull('activities.id')
        ->delete();

      Schema::table('activity_sessions', function (Blueprint $table) {
        $table->foreign('planning_id')->references('id')->on('plannings');
        $table->foreign('activity_id')->references('id')->on('activities');
      });
    } catch (\Throwable $th) {
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('activity_sessions', function (Blueprint $table) {
      //
    });
  }
}
