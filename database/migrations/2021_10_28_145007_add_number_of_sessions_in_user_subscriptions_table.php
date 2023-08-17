<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberOfSessionsInUserSubscriptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_subscriptions', function (Blueprint $table) {
      $table->integer('number_of_sessions')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('user_subscriptions', function (Blueprint $table) {
      $table->dropColumn('number_of_sessions');
    });
  }
}
