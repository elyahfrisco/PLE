<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldForSurbooking extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('plannings', function (Blueprint $table) {
      $table->integer('super_pass')->nullable()->default(0);
    });

    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->integer('super_pass')->nullable()->default(0);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('activity_sessions', function ($table) {
      $table->dropColumn('super_pass');
    });

    Schema::table('plannings', function ($table) {
      $table->dropColumn('super_pass');
    });
  }
}
