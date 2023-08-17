<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionActivitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('subscription_activities', function (Blueprint $table) {
      $table->id();
      $table->dateTime('date')->nullable();
      $table->integer('completed_session')->nullable()->default(0);
      $table->integer('session_count')->nullable()->default(0);
      $table->timestamps();
      $table->foreignId('planning_id');
      $table->foreignId('establishment_id');
      $table->foreignId('subscription_id')->references('id')->on('user_subscriptions');
      $table->foreignId('user_id');
      $table->foreignId('activity_id');
      $table->foreignId('pass_id');
      $table->foreignId('activity_session_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('subscription_activities');
  }
}
