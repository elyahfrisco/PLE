<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchKeyInPlanningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->string('search_key')->nullable();
        });
        Schema::table('user_phones', function (Blueprint $table) {
            $table->string('search_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plannings', function (Blueprint $table) {
            $table->dropColumn('search_key');
        });
        Schema::table('user_phones', function (Blueprint $table) {
            $table->dropColumn('search_key');
        });
    }
}
