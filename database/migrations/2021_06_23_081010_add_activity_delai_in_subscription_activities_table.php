<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityDelaiInSubscriptionActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_activities', function (Blueprint $table) {
            /** pour optimiser le nombre de requetes */
            $table->dateTime('time_start');
            $table->dateTime('time_end');
            $table->dateTime('absence_prevention_date')->nullable();
            /** pour optimiser le nombre de requetes */

            $table->dropColumn('completed_session');
            $table->dropColumn('session_count');
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
            $table->dropColumn('time_start');
            $table->dropColumn('time_end');
            $table->dropColumn('absence_prevention_date');
            $table->integer('completed_session')->nullable()->default(0);
            $table->integer('session_count')->nullable()->default(0);
        });
    }
}
