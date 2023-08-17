<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignIndexInBillsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('bills')
        ->leftJoin('seasons', 'bills.season_id', 'seasons.id')
        ->leftJoin('establishments', 'bills.establishment_id', 'establishments.id')
        ->whereNull('seasons.id')
        ->orWhereNull('establishments.id')
        ->delete();

      Schema::table('bills', function (Blueprint $table) {
        $table->foreign('season_id')->references('id')->on('seasons');
        $table->foreign('establishment_id')->references('id')->on('establishments');
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
    Schema::table('bills', function (Blueprint $table) {
      //
    });
  }
}
