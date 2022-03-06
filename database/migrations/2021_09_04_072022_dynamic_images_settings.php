<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DynamicImagesSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_image_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_name');
            $table->string('image_type');
            $table->string('image_position');
            $table->string('image_url');
            $table->string('image_redirect_url')->nullable();
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('dynamic_image_settings');
    }
}
