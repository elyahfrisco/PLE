<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    // Schema::table('user_subscriptions', function (Blueprint $table) {
    //     $table->foreign('user_id')->references('id')->on('users');
    //     $table->foreign('pass_id')->references('id')->on('passes');
    //     // $table->foreign('season_id')->references('id')->on('seasons');
    //     $table->foreign('bill_id')->references('id')->on('bills');
    //     $table->foreign('payment_id')->references('id')->on('payments')->nullOnDelete();
    //     $table->foreign('renewal_id')->references('id')->on('renewals')->nullOnDelete();
    //     $table->foreign('renewal_subscription_id')->references('id')->on('user_subscriptions')->nullOnDelete();
    // });

    // Schema::table('absence_preventions', function (Blueprint $table) {
    //     // $table->foreign('activity_session_id')->references('id')->on('activity_sessions');
    //     $table->foreign('user_id')->references('id')->on('users');
    //     $table->foreign('establishment_id')->references('id')->on('establishments');
    //     $table->foreign('activity_id')->references('id')->on('activities');
    //     $table->foreign('pass_id')->references('id')->on('passes');
    //     $table->foreign('season_id')->references('id')->on('seasons');
    //     $table->foreign('added_by_user_id')->references('id')->on('users')->nullOnDelete();
    // });

    // Schema::table('payments', function (Blueprint $table) {
    //     $table->foreign('bill_id')->references('id')->on('bills');
    //     $table->foreign('user_id')->references('id')->on('users');
    //     $table->foreign('admin_id')->references('id')->on('users');
    // });

    // Schema::table('prices', function (Blueprint $table) {
    //     // $table->foreign('trimester_id')->references('id')->on('trimesters');
    //     $table->foreign('activity_id')->references('id')->on('activities');
    //     $table->foreign('pass_id')->references('id')->on('passes');
    //     // $table->foreign('establishment_id')->references('id')->on('establishments');
    //     // $table->foreign('season_id')->references('id')->on('seasons');
    // });

    // Schema::table('queues', function (Blueprint $table) {
    //     // $table->foreign('activity_session_id')->references('id')->on('activity_sessions');
    //     // $table->foreign('absence_prevention_id')->references('id')->on('absence_preventions');
    //     $table->foreign('user_id')->references('id')->on('users');
    //     $table->foreign('pass_id')->references('id')->on('passes');
    //     $table->foreign('subscription_activity_id')->references('id')->on('subscription_activities');
    // });

    // Schema::table('recuperation_requests', function (Blueprint $table) {
    //     // $table->foreign('activity_session_id_to_catch_up')->references('id')->on('activity_sessions');
    //     // $table->foreign('activity_session_id_for_catch_up')->references('id')->on('activity_sessions');
    //     // $table->foreign('queue_id')->references('id')->on('queues');
    //     // $table->foreign('user_id')->references('id')->on('users');
    // });

    // Schema::table('renewals', function (Blueprint $table) {
    //     $table->foreign('payment_id')->references('id')->on('payments');
    //     $table->foreign('planning_id')->references('id')->on('plannings');
    //     $table->foreign('season_id')->references('id')->on('seasons');
    //     $table->foreign('activity_id')->references('id')->on('activities');
    //     // $table->foreign('subscription_id')->references('id')->on('subscriptions');
    //     $table->foreign('establishment_id')->references('id')->on('establishments');
    //     $table->foreign('activity_session_id')->references('id')->on('activity_sessions');
    // });


  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    // Schema::table('user_subscriptions', function (Blueprint $table) {
    //     $table->dropForeign(['user_id']);
    //     $table->dropForeign(['pass_id']);
    //     // $table->dropForeign(['season_id']);
    //     $table->dropForeign(['bill_id']);
    //     $table->dropForeign(['payment_id']);
    //     $table->dropForeign(['renewal_id']);
    //     $table->dropForeign(['renewal_subscription_id']);
    // });

    // Schema::table('activity_sessions', function (Blueprint $table) {
    //     $table->dropForeign(['planning_id']);
    //     $table->dropForeign(['activity_id']);
    // });

    // Schema::table('absence_preventions', function (Blueprint $table) {
    //     $table->dropForeign(['activity_session_id']);
    //     $table->dropForeign(['user_id']);
    //     $table->dropForeign(['establishment_id']);
    //     $table->dropForeign(['activity_id']);
    //     $table->dropForeign(['pass_id']);
    //     $table->dropForeign(['season_id']);
    //     $table->dropForeign(['added_by_user_id']);
    // });

    // Schema::table('bills', function (Blueprint $table) {
    //     $table->dropForeign(['season_id']);
    //     $table->dropForeign(['establishment_id']);
    // });

    // Schema::table('payments', function (Blueprint $table) {
    //     $table->dropForeign(['bill_id']);
    //     $table->dropForeign(['user_id']);
    //     $table->dropForeign(['admin_id']);
    // });

    // Schema::table('plannings', function (Blueprint $table) {
    //     $table->dropForeign(['establishment_id']);
    //     // $table->dropForeign(['season_id']);
    //     $table->dropForeign(['activity_id']);
    // });

    // Schema::table('prices', function (Blueprint $table) {
    //     // $table->dropForeign(['trimester_id']);
    //     $table->dropForeign(['activity_id']);
    //     $table->dropForeign(['pass_id']);
    //     // $table->dropForeign(['establishment_id']);
    //     // $table->dropForeign(['season_id']);
    // });

    // Schema::table('queues', function (Blueprint $table) {
    //     // $table->dropForeign(['activity_session_id']);
    //     // $table->dropForeign(['absence_prevention_id']);
    //     $table->dropForeign(['user_id']);
    //     $table->dropForeign(['pass_id']);
    //     $table->dropForeign(['subscription_activity_id']);
    // });

    // Schema::table('recuperation_requests', function (Blueprint $table) {
    //     // $table->dropForeign(['activity_session_id_to_catch_up']);
    //     // $table->dropForeign(['activity_session_id_for_catch_up']);
    //     // $table->dropForeign(['queue_id']);
    //     // $table->dropForeign(['user_id']);
    // });

    // Schema::table('renewals', function (Blueprint $table) {
    //     $table->dropForeign(['payment_id']);
    //     $table->dropForeign(['planning_id']);
    //     $table->dropForeign(['season_id']);
    //     $table->dropForeign(['activity_id']);
    //     // $table->dropForeign(['subscription_id']);
    //     $table->dropForeign(['establishment_id']);
    //     $table->dropForeign(['activity_session_id']);
    // });

    // Schema::table('role_user', function (Blueprint $table) {
    //     // $table->dropForeign(['user_id']);
    //     $table->dropForeign(['role_id']);
    // });

    // Schema::table('subscription_activities', function (Blueprint $table) {
    //     // $table->dropForeign(['planning_id']);
    //     // $table->dropForeign(['establishment_id']);
    //     // $table->dropForeign(['subscription_id']);
    //     // $table->dropForeign(['user_id']);
    //     // $table->dropForeign(['activity_id']);
    //     // $table->dropForeign(['pass_id']);
    //     // $table->dropForeign(['activity_session_id']);
    //     $table->dropForeign(['absence_prevention_id']);
    // });
  }
}
