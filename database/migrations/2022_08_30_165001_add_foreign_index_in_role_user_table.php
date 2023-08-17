<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignIndexInRoleUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    try {
      DB::table('role_user')
        ->leftJoin('users', 'role_user.user_id', 'users.id')
        ->whereNull('users.id')
        ->delete();

      Schema::table('role_user', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        $table->foreign('role_id')->references('id')->on('roles');
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
    Schema::table('role_user', function (Blueprint $table) {
      //
    });
  }
}
