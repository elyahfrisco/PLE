<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstablishmentActivityTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('establishment_activity', function (Blueprint $table) {
      $table->foreignId('establishment_id')->references('id')->on('establishments');
      $table->foreignId('activity_id');
      $table->boolean('out_pass')->default(false);
      $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('establishment_activity');
  }
}
