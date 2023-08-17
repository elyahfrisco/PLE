<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bugs', function (Blueprint $table) {
      $table->id();
      $table->longText('title')->nullable();
      $table->longText('content')->nullable();
      $table->longText('page')->nullable();
      $table->boolean('resolved')->nullable()->default(false);
      $table->timestamps();
      $table->foreignId('user_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('bugs');
  }
}
