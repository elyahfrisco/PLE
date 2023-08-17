<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchKeyInActivitySession extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('activity_sessions', function (Blueprint $table) {
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
    Schema::table('activity_sessions', function (Blueprint $table) {
      $table->dropColumn('search_key');
    });
  }
}
