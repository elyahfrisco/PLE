<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditUserRelaunchsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_relaunchs', function (Blueprint $table) {
      $table->longText('content')->change();
      $table->boolean('executed')->nullable()->default(false)->change();
      $table->foreignId('season_id')->nullable();
      $table->foreignId('pass_id')->nullable();
      $table->foreignId('num_trimester')->nullable();
      $table->string('id_group')->nullable();
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
      $table->dropColumn('season_id');
      $table->dropColumn('pass_id');
      $table->dropColumn('num_trimester');
      $table->dropColumn('id_group');
    });
  }
}
