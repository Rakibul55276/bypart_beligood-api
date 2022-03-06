<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_activity', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('view_count');
            $table->unsignedBigInteger('listing_id');
            $table->timestamps();
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
        Schema::dropIfExists('listing_activity');
    }
}
