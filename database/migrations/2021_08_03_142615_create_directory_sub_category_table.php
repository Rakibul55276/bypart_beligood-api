<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectorySubCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_sub_category', function (Blueprint $table) {
            $table->id();
            $table->string('sub_category_name');
            $table->string('sub_category_icon')->nullable();
            $table->unsignedBigInteger('directory_category_id');
            $table->integer('is_popular')->default(0);

            //Foregin key constrains
            $table->foreign('directory_category_id')->references('id')->on('directory_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directory_sub_category');
    }
}
