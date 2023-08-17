<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFeesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_fees', function (Blueprint $table) {
      $table->id();
      $table->float('amount');
      $table->string('type');
      /**
       * management
       * registration
       * */
      $table->timestamps();
      $table->foreignId('bill_id');
      $table->foreignId('user_id');
      $table->foreignId('season_id')->nullable();
    });

    Schema::table('payments', function (Blueprint $table) {
      $table->foreignId('bill_id');
      $table->foreignId('user_id');
      $table->foreignId('admin_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_fees');

    Schema::table('payments', function (Blueprint $table) {
      $table->dropColumn('bill_id');
      $table->dropColumn('user_id');
      $table->dropColumn('admin_id');
    });
  }
}
