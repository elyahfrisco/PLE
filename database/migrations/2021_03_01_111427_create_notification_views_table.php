<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationViewsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('notification_views', function (Blueprint $table) {
      $table->id();
      $table->timestamps();

      $table->unsignedBigInteger('user_id')->foreign()->references('id')->on('users');
      $table->unsignedBigInteger('notification_id')->foreign()->references('id')->on('notifications');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('notification_views');
  }
}
