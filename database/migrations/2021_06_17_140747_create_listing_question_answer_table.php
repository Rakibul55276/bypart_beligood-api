<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingQuestionAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_question_answer', function (Blueprint $table) {
            $table->id();

            $table->text('question')->nullable();
            $table->text('question_images')->nullable();
            $table->integer('question_view_count')->nullable();

            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('question_asked_by_user_id');
            $table->timestamps();

            //Foregin key constraints
            $table->foreign('question_asked_by_user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('listing_question_answer');
    }
}
