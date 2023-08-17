<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignIndexInUserCommentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {

    try {
      DB::table('user_comments')
        ->leftJoin('users', 'user_comments.user_id', 'users.id')
        ->whereNull('users.id')
        ->delete();

      Schema::table('user_comments', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
      });
    } catch (\Throwable $th) {
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('user_comments', function (Blueprint $table) {
    });
  }
}
