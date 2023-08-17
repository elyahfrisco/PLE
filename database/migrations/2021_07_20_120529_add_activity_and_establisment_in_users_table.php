<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityAndEstablismentInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('establishment_id')->nullable();
            $table->foreignId('activity_id')->nullable();
            $table->boolean('is_imported')->nullable()->default(false);
            $table->string('code_client')->nullable();
            $table->string('default_password')->nullable();
            $table->date('birth_date')->nullable()->change();
            $table->string('gender')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('establishment_id');
            $table->dropColumn('activity_id');
            $table->dropColumn('is_imported');
            $table->dropColumn('code_client');
            $table->dropColumn('default_password');
        });
    }
}
