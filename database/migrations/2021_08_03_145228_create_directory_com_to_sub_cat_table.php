<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoryComToSubCatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_com_to_sub_cat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dir_sub_category_id');
            $table->unsignedBigInteger('dir_company_info_id');

            //Foregin key constrains
            $table->foreign('dir_company_info_id')->references('id')->on('directory_company_info')->onDelete('cascade');
            $table->foreign('dir_sub_category_id')->references('id')->on('directory_sub_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('directory_com_to_sub_cat');
    }
}
