<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoryCompanyInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directory_company_info', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 150)->nullable();
            $table->string('company_logo', 150)->nullable();
            $table->string('company_email', 100)->unique()->nullable();
            $table->string('company_phone_number')->nullable();
            $table->text('address')->nullable();
            $table->text('company_description')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('company_url')->nullable();

            $table->string('name_card_file')->nullable();
            $table->text('premise_pictures')->nullable();

            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->string('operating_hour')->nullable();
            $table->string('premium_company_image')->nullable();

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
        Schema::dropIfExists('directory_company_info');
    }
}
