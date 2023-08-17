<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherResponsePlaceholderCulumnInActivitySubscriptionQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_subscription_questionnaires', function (Blueprint $table) {
            $table->string('other_response_placeholder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_subscription_questionnaires', function (Blueprint $table) {
            $table->dropColumn('other_response_placeholder');
        });
    }
}
