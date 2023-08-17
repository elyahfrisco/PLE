<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnsMailInUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->renameColumn('parent_mail1', 'mail1');
      $table->renameColumn('parent_mail2', 'mail2');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->renameColumn('mail1', 'parent_mail1');
      $table->renameColumn('mail2', 'parent_mail2');
    });
  }
}
