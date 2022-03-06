<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BypartForumComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_comment', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('post_id')->nullable();
            $table->longText('comment')->nullable();
            $table->longText('images')->nullable();
            $table->string('parent_comment_id')->nullable();
            $table->integer('like_count')->default(0);
            $table->integer('is_deleted')->default(0);
            $table->integer('report_count')->default(0);
            $table->longText('report_text')->nullable();
            $table->string('reply_type')->nullable();

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
        Schema::dropIfExists('forum_comment');
    }
}
