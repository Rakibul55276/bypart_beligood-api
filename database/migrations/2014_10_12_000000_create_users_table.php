<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('password_reset_token')->nullable();

            $table->string('mobile_no')->nullable();
            $table->boolean('is_mobile_verified')->default(false);
            $table->string('mobile_verification_code')->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            $table->string('avatar')->nullable();
            $table->longText('user_ic_photo')->nullable();
            $table->string('user_ic_number')->nullable();

            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->string('remember_token', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->boolean('is_email_verified')->default(false);

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            $table->integer('company_id')->nullable();
            //User point
            $table->bigInteger('bp_point')->default(0);
            $table->enum('user_type', ['admin','agent','user','dealer','other'])->default('other');
            $table->enum('status', ['approve','deactive','banned','deleted','none'])->default('none');
            $table->enum('status_admin', ['approve','dis_approve','under_review'])->default('under_review');

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
        Schema::dropIfExists('users');
    }
}
