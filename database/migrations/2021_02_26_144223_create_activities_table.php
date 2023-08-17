<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activities', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->longText('description');
      $table->time('duration');
      $table->string('background_color')->default('#0c5d89');
      $table->string('font_color')->default('#FFFFFF');
      $table->boolean('care')->nullable()->default(false);
      $table->boolean('for_kid')->nullable()->default(false);
      $table->softDeletes();
      $table->timestamps();
      $table->foreignId('activity_category_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('activities');
  }
}
