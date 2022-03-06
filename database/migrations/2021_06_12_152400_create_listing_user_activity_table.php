<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingUserActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_user_activity', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->boolean('is_bought')->default(false);
            $table->string('buy_now_reference')->nullable();
            $table->timestamp('buy_time')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('listing_id');

            $table->timestamps();
            //Foregin key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('listing_id')->references('id')->on('listing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_user_activity');
    }
}
