<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignIndexInSubscriptionCommentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('subscription_comments')
        ->leftJoin('user_subscriptions', 'subscription_comments.user_subscription_id', 'user_subscriptions.id')
        ->whereNull('user_subscriptions.id')
        ->delete();

      Schema::table('subscription_comments', function (Blueprint $table) {
        $table->foreign('user_subscription_id')->references('id')->on('user_subscriptions')->cascadeOnDelete();
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
    Schema::table('subscription_comments', function (Blueprint $table) {
      //
    });
  }
}
