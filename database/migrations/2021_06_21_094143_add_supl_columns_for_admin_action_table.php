<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuplColumnsForAdminActionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('absence_preventions', function (Blueprint $table) {
      $table->foreignId('added_by_user_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('absence_preventions', function (Blueprint $table) {
      $table->dropColumn('added_by_user_id')->nullable();
    });
  }
}
