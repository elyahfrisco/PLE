<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldForFeatRelaunch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_relaunchs', function (Blueprint $table) {
            $table->foreignId('bill_id')->nullable();
            $table->foreignId('subscription_id')->nullable();
            $table->foreignId('work_id')->nullable();
            $table->boolean('written_manually')->nullable()->default(false);
        });

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->boolean('template_for_app_notification')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_relaunchs', function (Blueprint $table) {
            $table->dropColumn('bill_id');
            $table->dropColumn('subscription_id');
            $table->dropColumn('work_id');
            $table->dropColumn('written_manually');
        });

        Schema::table('mail_templates', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('template_for_app_notification');
        });
    }
}
