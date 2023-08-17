<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->longText('content');
            $table->longText('link');
            $table->integer('level');
            $table->dateTime('publish_at');
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->foreign()->references('id')->on('users');
            $table->unsignedBigInteger('user_relaunch_id')->foreign()->references('id')->on('user_relaunchs');
            $table->unsignedBigInteger('queue_id')->foreign()->references('id')->on('queues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
