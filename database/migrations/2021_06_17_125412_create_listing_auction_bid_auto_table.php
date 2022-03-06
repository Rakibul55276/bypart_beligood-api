<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingAuctionBidAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_auction_bid_auto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('user_id');

            $table->boolean('is_email_reminder_on')->default(false);
            $table->boolean('is_auto_bid_enable')->default(false);

            $table->boolean('is_out_bid_notification_sent')->default(false);

            $table->bigInteger('max_auto_bid_amount')->default(0);

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
        Schema::dropIfExists('listing_auction_bid_auto');
    }
}
