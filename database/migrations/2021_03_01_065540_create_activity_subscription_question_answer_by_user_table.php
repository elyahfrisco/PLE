<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitySubscriptionQuestionAnswerByUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('activity_subscription_question_answer_by_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id');
      $table->foreignId('activity_subscription_questionnaire_id');
      $table->foreignId('activity_subscription_question_answer_id');
      $table->string('response');
      $table->string('other_response');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('activity_subscription_question_answer_by_user');
  }
}
