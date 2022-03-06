<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing', function (Blueprint $table) {
            $table->id();
            // Common details for classified and auction
            $table->enum('car_condition', ['new','used','recon'])->default('new')->nullable();
            // Following only applicable for used car
            $table->string('car_ownership_document')->nullable();
            $table->string('car_plate_verification_image')->nullable();
            $table->string('car_plate_number')->nullable();
            $table->string('inspection_report')->nullable();

            $table->json('car_images')->nullable();
            $table->string('car_body_type')->nullable();
            $table->string('car_make_name')->nullable();
            $table->string('mileage')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacture_year')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('doors')->nullable();
            $table->string('seats')->nullable();
            $table->string('engine_size')->nullable();
            $table->string('color')->nullable();
            $table->enum('listing_type', ['classified', 'auction']);
            $table->string('ad_title')->nullable();
            $table->text('ad_description')->nullable();
            $table->string('state')->nullable();
            $table->string('area')->nullable();
            $table->string('variant')->nullable();
            // Classified field only, nullable incase listing is auction
            $table->bigInteger('asking_price')->nullable();

            //Auction related, Need to be nullable incase listing is classified
            $table->bigInteger('starting_price')->nullable();
            $table->bigInteger('reserve_price')->nullable();
            $table->bigInteger('buy_now_price')->nullable();
            $table->string('duration_of_auction')->nullable();
            $table->timestamp('start_date', 0)->nullable();
            $table->timestamp('end_date', 0)->nullable();

            //Other status realted
            $table->enum('listing_status', ['draft','pending_approval','published','sold','unsold','deleted','expired','rejected','archived'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_highlight')->default(false);
            $table->timestamp('highlight_end_date')->nullable();

            $table->boolean('is_feature')->default(false);
            $table->timestamp('feature_end_date')->nullable();

            $table->unsignedBigInteger('categories_id');
            $table->unsignedBigInteger('user_id');

            $table->timestamp('list_relist_date', 0)->nullable();
            $table->timestamps();

            //Foregin key constrains
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('categories_id')->references('id')->on('sub_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing');
    }
}
