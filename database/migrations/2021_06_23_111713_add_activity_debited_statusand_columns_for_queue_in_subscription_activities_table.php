<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityDebitedStatusandColumnsForQueueInSubscriptionActivitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('subscription_activities', function (Blueprint $table) {
      $table->boolean('is_debited')->default(false)->nullable();
      $table->dateTime('queued_at')->nullable();
      $table->dateTime('can_catch_up_until')->nullable();
      $table->string('session_status_txt')->nullable();
      $table->string('presence_status_txt')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('subscription_activities', function (Blueprint $table) {
      $table->dropColumn('is_debited');
      $table->dropColumn('queued_at');
      $table->dropColumn('can_catch_up_until');
      $table->dropColumn('session_status_txt');
      $table->dropColumn('presence_status_txt');
    });
  }
}
