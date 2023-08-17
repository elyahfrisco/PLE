<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;

class CreateFollowedUserTable extends Migration
{
  use SoftDeletes;
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('followed_user', function (Blueprint $table) {
      $table->id();
      $table->boolean('accepted');
      $table->dateTime('acceptation_date');
      $table->timestamps();
      $table->softDeletes();

      $table->foreignId('user_follower_id')->references('id')->on('users');
      $table->foreignId('user_following_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('followed_user');
  }
}
