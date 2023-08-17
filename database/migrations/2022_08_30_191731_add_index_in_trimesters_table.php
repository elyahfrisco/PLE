<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexInTrimestersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('trimesters')
        ->leftJoin('seasons', 'trimesters.season_id', 'seasons.id')
        ->whereNull('seasons.id')
        ->delete();

      Schema::table('trimesters', function (Blueprint $table) {
        $table->foreign('season_id')->references('id')->on('seasons')->cascadeOnDelete();
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
    Schema::table('trimesters', function (Blueprint $table) {
      //
    });
  }
}
