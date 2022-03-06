<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectorySubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('directory_sub_category')->insert([
            'id' => 1,
            "sub_category_name" => "Accessories Retail",
            "directory_category_id" => 1
        ]);

        DB::table('directory_sub_category')->insert([
            'id' => 2,
            "sub_category_name" => "Audio System - In Car Entertainment",
            "directory_category_id" => 1
        ]);

        DB::table('directory_sub_category')->insert([
            'id' => 3,
            "sub_category_name" => "Car Mats",
            "directory_category_id" => 1
        ]);

        DB::table('directory_sub_category')->insert([
            'id' => 4,
            "sub_category_name" => "Security System",
            "directory_category_id" => 1
        ]);

        DB::table('directory_sub_category')->insert([
            'id' => 5,
            "sub_category_name" => "Bodykits & Parts",
            "directory_category_id" => 2
        ]);

        DB::table('directory_sub_category')->insert([
            'id' => 6,
            "sub_category_name" => "Handling & Safety",
            "directory_category_id" => 2
        ]);

        DB::table('directory_sub_category')->insert([
            'id' =>7,
            "sub_category_name" => "Car Care Products",
            "directory_category_id" => 3
        ]);

        DB::table('directory_sub_category')->insert([
            'id' =>8,
            "sub_category_name" => "Mobile Car Grooming",
            "directory_category_id" => 3
        ]);

        DB::table('directory_sub_category')->insert([
            'id' =>9,
            "sub_category_name" => " Car Auctions",
            "directory_category_id" => 5
        ]);

        DB::table('directory_sub_category')->insert([
            'id' =>10,
            "sub_category_name" => "Car Leasing",
            "directory_category_id" => 5
        ]);
    }
}
