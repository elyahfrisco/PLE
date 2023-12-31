<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientDisplayPlanningConditionColumnsInPlanningsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('plannings', function (Blueprint $table) {
      $table->boolean('hide_to_customer')->default(false)->nullable();
    });
    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->boolean('hide_to_customer')->default(false)->nullable();
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
      $table->dropColumn('hide_to_customer');
    });
    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->dropColumn('hide_to_customer');
    });
  }
}
