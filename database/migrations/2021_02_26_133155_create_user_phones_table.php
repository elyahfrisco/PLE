<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPhonesTable extends Migration
{
  use SoftDeletes;
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_phones', function (Blueprint $table) {
      $table->id();
      $table->string('phone');
      $table->string('owner')->nullable();
      /* MME | MR | Autre */
      $table->string('type')->nullable();
      $table->softDeletes();
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
    Schema::dropIfExists('user_phones', function (Blueprint $table) {
      $table->dropSoftDeletes();
    });
  }
}
