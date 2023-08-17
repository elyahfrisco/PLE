<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenewalsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('renewals', function (Blueprint $table) {
      $table->id();
      $table->boolean('stop');
      $table->boolean('continue');
      $table->boolean('change');
      $table->boolean('accepted');
      $table->dateTime('foreseeable_payment_date');
      $table->boolean('settled');
      $table->dateTime('validation_date');
      $table->timestamps();

      $table->foreignId('payment_id');
      $table->foreignId('user_subscription_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('renewals');
  }
}
