<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_subscriptions', function (Blueprint $table) {
      $table->id();
      $table->date('expired_at')->nullable();
      $table->string('subscription_type');
      $table->float('amount')->nullable();
      $table->timestamps();

      $table->foreignId('user_id');
      $table->foreignId('pass_id');
      $table->foreignId('season_id');
      $table->foreignId('payment_id')->nullable();
      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('bill_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_subscriptions');
  }
}
