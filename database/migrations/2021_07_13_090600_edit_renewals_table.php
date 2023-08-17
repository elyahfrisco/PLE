<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('renewals', function (Blueprint $table) {
            $table->boolean('stop')->nullable()->change();
            $table->boolean('continue')->nullable()->change();
            $table->boolean('change')->nullable()->change();
            $table->boolean('accepted')->nullable()->change();
            $table->dateTime('foreseeable_payment_date')->nullable()->change();
            $table->boolean('settled')->nullable()->change();
            $table->dateTime('validation_date')->nullable()->change();
            $table->foreignId('payment_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
