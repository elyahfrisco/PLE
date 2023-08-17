<?php

use App\Models\ActivitySessions;
use App\Models\Planning;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexInPlanningsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $plannings_id = DB::table('plannings')
      ->leftJoin('establishments', 'plannings.establishment_id', 'establishments.id')
      ->leftJoin('seasons', 'plannings.season_id', 'seasons.id')
      ->leftJoin('activities', 'plannings.activity_id', 'activities.id')
      ->whereNull('establishments.id')
      ->orWhereNull('seasons.id')
      ->orWhereNull('activities.id')
      ->pluck('plannings.id');

    ActivitySessions::whereIn('planning_id', $plannings_id)->delete();
    Planning::whereIn('id', $plannings_id)->delete();

    Schema::table('plannings', function (Blueprint $table) {
      $table->foreignId('season_id')->nullable(false)->change();
    });

    Schema::table('plannings', function (Blueprint $table) {
      $table->foreign('establishment_id')->references('id')->on('establishments');
      $table->foreign('activity_id')->references('id')->on('activities');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('plannings', function (Blueprint $table) {
      //
    });
  }
}
