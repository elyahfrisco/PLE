<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnsInQueuesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('queues', function (Blueprint $table) {
      /**
       * for_subscription
       * for_recuperation
       * */
      $table->string('type')->nullable()->default('for_recuperation');
      $table->integer('priority')->nullable()->default(0)->change();
      $table->foreignId('absence_prevention_id')->nullable()->change();

      /** new columns */
      $table->foreignId('user_id');
      $table->foreignId('pass_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('queues', function (Blueprint $table) {
      $table->dropColumn('user_id');
      $table->dropColumn('pass_id');
      $table->dropColumn('type');
    });
  }
}
