<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenewalsStatusInRenewalsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('renewals', function (Blueprint $table) {
      $table->string('renewal_status');
      $table->foreignId('planning_id')->nullable();
      $table->foreignId('season_id')->nullable();
      $table->foreignId('activity_id')->nullable();
      $table->foreignId('subscription_id')->nullable();
      $table->string('day')->nullable();
      $table->dropColumn('user_subscription_id');
      $table->integer('num_trimester')->nullable();
      $table->foreignId('establishment_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('renewals', function (Blueprint $table) {
      $table->dropColumn('renewal_status');
      $table->dropColumn('planning_id');
      $table->dropColumn('season_id');
      $table->dropColumn('activity_id');
      $table->dropColumn('subscription_id');
      $table->dropColumn('day');
      $table->foreignId('user_subscription_id')->nullable();
      $table->dropColumn('num_trimester');
      $table->dropColumn('establishment_id');
    });
  }
}
