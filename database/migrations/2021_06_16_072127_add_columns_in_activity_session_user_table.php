<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInActivitySessionUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    /** for coach | intervenant exterieur  */
    Schema::table('activity_session_user', function (Blueprint $table) {
      $table->boolean('accomplished')->default(false)->nullable();
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
    Schema::table('activity_session_user', function (Blueprint $table) {
      $table->dropColumn('accomplished');
      $table->dropColumn('absence_prevention_id');
    });
  }
}
