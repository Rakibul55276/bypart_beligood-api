<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarMakeRequstsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_make_requsts', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model');
            $table->string('manufactured_year');
            $table->string('manufactured_country');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->string('engine_capacity');
            $table->string('variant')->nullable();
            $table->string('condition');
            $table->string('supporting_document')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            //Foregin key constrains
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_make_requsts');
    }
}
