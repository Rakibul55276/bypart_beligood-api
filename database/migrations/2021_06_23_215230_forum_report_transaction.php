<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForumReportTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_report_transaction', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('transaction_type')->nullable();// post or comment
            $table->integer('transaction_id')->nullable();
            $table->string('report_text')->nullable();
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
        Schema::dropIfExists('forum_report_transaction');
    }
}
