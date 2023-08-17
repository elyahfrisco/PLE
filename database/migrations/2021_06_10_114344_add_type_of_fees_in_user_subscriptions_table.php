<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeOfFeesInUserSubscriptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_subscriptions', function (Blueprint $table) {
      $table->string('type_of_fees')->nullable()->default('normal');
      /**
       * normal
       * reduced
       * offered
       */
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
      $table->dropColumn('type_of_fees');
    });
  }
}
