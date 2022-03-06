<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_array=array('Car Related','Non-Car Related','Auction Car','Classified Car','Motor Car','Classic Car','European Car');

        $insertArray=array();
        foreach ($category_array as $key => $value) {
        	$insertArray[$key]['name']=$value;
        }

        DB::table('forum_category')->insert($insertArray);
    }
}
