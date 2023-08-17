<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelaunchsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_relaunchs', function (Blueprint $table) {
      $table->id();
      $table->string('content');
      $table->string('relaunch_type');
      $table->dateTime('date_relaunch');
      $table->boolean('executed');
      $table->timestamps();

      $table->foreignId('user_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_relaunchs');
  }
}
