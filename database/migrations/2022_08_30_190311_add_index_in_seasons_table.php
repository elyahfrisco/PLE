<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexInSeasonsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('seasons')
        ->leftJoin('establishments', 'seasons.establishment_id', 'establishments.id')
        ->whereNull('establishments.id')
        ->delete();

      Schema::table('seasons', function (Blueprint $table) {
        $table->foreign('establishment_id')->references('id')->on('establishments')->cascadeOnDelete();
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
    Schema::table('seasons', function (Blueprint $table) {
      //
    });
  }
}
