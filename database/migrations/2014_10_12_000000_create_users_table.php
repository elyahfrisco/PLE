<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  use SoftDeletes;
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable();
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->rememberToken();
      $table->foreignId('current_team_id')->nullable();
      $table->text('profile_photo_path')->nullable();
      $table->timestamps();

      $table->string('first_name');
      $table->date('birth_date');
      $table->enum('gender', ['male', 'female', 'child']);
      $table->string('address');
      $table->string('postal_code');
      $table->string('city');
      $table->string('contact_origin')->nullable();
      $table->longText('precision_contact_origin')->nullable();
      $table->boolean('registration_promo')->default(0);
      $table->longText('additional_information')->nullable();
      $table->string('medical_certificate_path')->nullable();
      $table->string('contact_profile')->nullable();
      $table->string('speciality')->nullable();
      $table->dateTime('signature_date')->nullable();
      $table->boolean('activated')->default(0);
      $table->dateTime('status_changed_at')->nullable();

      /* <child info> */
      $table->string('city_birth')->nullable();
      $table->string('maternity_name')->nullable();
      $table->string('used_with_other_people')->default(0);
      $table->string('childcare')->nullable();
      /* </child info> */

      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('users');
  }
}
