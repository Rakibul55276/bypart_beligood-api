<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertArray=array();

    	$user_id_array=array(2,5,7,9,11,19,21);
    	$category_array=array(1,2,3,4);
    	for ($i=1; $i <= 200; $i++) { 
    		$user_key=array_rand($user_id_array);
    		$cat_key=array_rand($category_array);

    		$insertArray[$i]['user_id']=$user_id_array[$user_key];
    		$insertArray[$i]['category']=$category_array[$cat_key];
    		$insertArray[$i]['subject']="This is Subject ".$i;
    		$insertArray[$i]['post']="This Post is Auto Generated ".$i;
    		$insertArray[$i]['cover_image']="https://bootdey.com/img/Content/avatar/avatar7.png";
    	}

    	$insert=DB::table("forum")->insert($insertArray);
    }
}
