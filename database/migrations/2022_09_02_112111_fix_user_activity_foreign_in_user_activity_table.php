<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUserActivityForeignInUserActivityTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::table('user_activity')
      ->leftJoin('users', 'user_activity.user_id', 'users.id')
      ->leftJoin('activities', 'user_activity.activity_id', 'activities.id')
      ->whereNull('users.id')
      ->orwhereNull('activities.id')
      ->delete();
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
