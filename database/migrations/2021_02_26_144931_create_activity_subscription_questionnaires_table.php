<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySubscriptionQuestionnairesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activity_subscription_questionnaires', function (Blueprint $table) {
      $table->id();
      $table->enum('type', ['single', 'multiple']);
      $table->text('content');
      $table->boolean('other_response');
      $table->timestamps();
      $table->foreignId('activity_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('activity_subscription_questionnaires');
  }
}
