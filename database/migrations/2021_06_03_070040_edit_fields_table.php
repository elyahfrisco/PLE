<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditFieldsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('posts', function (Blueprint $table) {
      $table->foreignId('post_category_id');
    });

    Schema::table('post_categories', function (Blueprint $table) {
      $table->string('name');
      $table->string('color');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('posts', function ($table) {
      $table->dropColumn('post_category_id');
    });
    Schema::table('post_categories', function ($table) {
      $table->dropColumn('name');
      $table->dropColumn('color');
    });
  }
}
