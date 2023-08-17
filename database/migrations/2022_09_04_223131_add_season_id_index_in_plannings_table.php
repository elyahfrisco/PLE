<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSeasonIdIndexInPlanningsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::table('plannings')
      ->leftJoin('seasons', 'plannings.season_id', 'seasons.id')
      ->whereNull('seasons.id')
      ->delete();

    Schema::table('plannings', function (Blueprint $table) {
      $table->foreign('season_id')->references('id')->on('seasons');
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
