<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingQuestionAnswerReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_question_answer_reply', function (Blueprint $table) {
            $table->id();

            $table->text('reply')->nullable();
            $table->text('reply_images')->nullable();
            $table->unsignedBigInteger('listing_question_answer_id');
            $table->unsignedBigInteger('question_reply_by_user_id');

            $table->timestamps();
            //Foregin key constraints
            $table->foreign('listing_question_answer_id')->references('id')->on('listing_question_answer')->onDelete('cascade');
            $table->foreign('question_reply_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_question_answer_reply');
    }
}
