<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditTableForUserPointing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_activities', function (Blueprint $table) {
            $table->boolean('accomplished')->nullable()->default(0);
            $table->boolean('is_recuperation')->nullable()->default(0);
            $table->foreignId('absence_prevention_id')->nullable();
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
            $table->dropColumn('accomplished');
            $table->dropColumn('is_recuperation');
            $table->dropColumn('absence_prevention_id');
        });
    }
}
