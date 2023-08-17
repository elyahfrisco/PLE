<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenewedColumnInUserSubscriptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_subscriptions', function (Blueprint $table) {
      $table->foreignId('renewal_subscription_id')->nullable();
      $table->foreignId('renewal_id')->nullable();
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
      $table->dropColumn('renewal_subscription_id');
      $table->dropColumn('renewal_id');
    });
  }
}
