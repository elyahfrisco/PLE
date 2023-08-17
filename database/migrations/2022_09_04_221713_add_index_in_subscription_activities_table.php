<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndexInSubscriptionActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plannings_id = DB::table('subscription_activities')
            ->leftJoin('plannings', 'subscription_activities.planning_id', 'plannings.id')
            ->leftJoin('establishments', 'subscription_activities.establishment_id', 'establishments.id')
            ->leftJoin('users', 'subscription_activities.user_id', 'users.id')
            ->leftJoin('activities', 'subscription_activities.activity_id', 'activities.id')
            ->leftJoin('passes', 'subscription_activities.pass_id', 'passes.id')
            ->leftJoin('activity_sessions', 'subscription_activities.activity_session_id', 'activity_sessions.id')

            ->whereNull('plannings.id')
            ->orWhereNull('establishments.id')
            ->orWhereNull('users.id')
            ->orWhereNull('activities.id')
            ->orWhereNull('passes.id')
            ->orWhereNull('activity_sessions.id')

            ->delete();

        Schema::table('subscription_activities', function (Blueprint $table) {
            $table->foreign('planning_id')->references('id')->on('plannings');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('pass_id')->references('id')->on('passes');
            $table->foreign('activity_session_id')->references('id')->on('activity_sessions');
            // $table->foreign('absence_prevention_id')->references('id')->on('absence_preventions');
        });
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
