<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityGroupsOfAPassTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activity_groups_of_a_pass', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('select_mode', ['or', 'and'])->default('or');
      $table->integer('number_session')->nullable();
      $table->timestamps();

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
    Schema::dropIfExists('activity_groups_of_a_pass');
  }
}
