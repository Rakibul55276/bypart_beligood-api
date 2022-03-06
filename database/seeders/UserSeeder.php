<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            'id' => 1,
            "first_name" => "Admin",
            "last_name" => "User",
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
            "mobile_no" => "01234567890",
            'is_mobile_verified' => true,
            'user_type' => 'admin',
            'status' => 'approve'
        ]);

        DB::table('users')->insert([
            'id' => 2,
            "first_name" => "Shojibur",
            "last_name" => "Rahman",
            'email' => 'shojibur@gmail.com',
            'password' => bcrypt('12345678'),
            "mobile_no" => "01234567890",
            'is_mobile_verified' => true,
            'is_email_verified' => true,
            'bp_point' => '500',
            'user_type' => 'user',
            'status' => 'approve'
        ]);

        DB::table('users')->insert([
            'id' => 3,
            "first_name" => "shoji",
            "last_name" => "Agent account",
            'email' => 'shojibur.rahman@bebit.com',
            'password' => bcrypt('12345678'),
            "mobile_no" => "01234567890",
            'is_mobile_verified' => true,
            'is_email_verified' => true,
            'bp_point' => '500',
            'user_type' => 'agent',
            'status' => 'approve'
        ]);

        DB::table('users')->insert([
            'id' => 4,
            "first_name" => "Shojibur ",
            "last_name" => "Dealer",
            'email' => '99codershojibur@gmail.com',
            'password' => bcrypt('12345678'),
            "mobile_no" => "01234567890",
            'is_mobile_verified' => true,
            'is_email_verified' => true,
            'bp_point' => '1000',
            'user_type' => 'dealer',
            'status' => 'approve'
        ]);
    }
}
