<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 150)->nullable();
            $table->string('company_logo', 150)->nullable();
            $table->string('company_email', 100)->unique()->nullable();
            $table->string('company_phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('new_company_registration_no')->nullable();
            $table->string('old_cpompany_registration_no')->nullable();
            $table->string('company_url')->nullable();

            $table->longText('company_cert_ssm_file')->nullable();
            $table->longText('other_supporting_files')->nullable();
            $table->longText('name_card_file')->nullable();
            $table->longText('premise_pictures')->nullable();

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
        Schema::dropIfExists('companies');
    }
}
