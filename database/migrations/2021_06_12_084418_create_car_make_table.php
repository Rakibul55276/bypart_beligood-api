<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarMakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_make', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model_name')->nullable();

            $table->string('variant')->nullable();
            $table->string('generation')->nullable();// This is model code

            $table->string('min_year')->nullable();
            $table->string('max_year')->nullable();
            $table->text('min_max_year')->nullable();//To hold scripted yeat base on min and max

            $table->string('fuel_type')->nullable(); // also engine type
            $table->string('car_body_type')->nullable();

            $table->string('door')->nullable();

            $table->string('seat')->nullable();
            $table->string('engine_size')->nullable();
            $table->string('engine_code')->nullable();

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
        Schema::dropIfExists('car_make');
    }
}
