<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuplInfoColumnsInBillsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('bills', function (Blueprint $table) {
      $table->foreignId('season_id');
      $table->foreignId('establishment_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('bills', function (Blueprint $table) {
      $table->dropColumn('season_id');
      $table->dropColumn('establishment_id');
    });
  }
}
