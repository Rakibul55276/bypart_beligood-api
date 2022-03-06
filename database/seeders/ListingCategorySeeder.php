<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_array=array(
            'Vehicles'=>['Cars','Car Plates','Car Accesories & Parts','Motorcycle','Commercial Vehicles'],
            'Electronics'=>[],
            'Household & Kitchen Equipment'=>[],
            'Hobbies & Sport Equipment'=>[],
            'Health & Beauty'=>[],
            'Properties'=>[],
            'Jobs & Services'=>[],
            'Fashion'=>[],
            'F&B Grocery'=>[],
        );

        $active_key = ['Vehicles'];
        $active_sub_key = ['Cars','Car Plates'];

        $insertArray=array();
        $insertSubArray=array();
        $id = 1;
        $sub_id = 1;

        foreach ($category_array as $key => $value) {
            $insertArray[$key]['id'] = $id;
        	$insertArray[$key]['name'] = $key;
            $insertArray[$key]['is_active'] = in_array($key,$active_key);
            foreach ($category_array[$key] as $subkey=>$subvalue){
                $insertSubArray[$subvalue]['id'] = $sub_id;
                $insertSubArray[$subvalue]['name'] = $subvalue;
                $insertSubArray[$subvalue]['is_active'] = in_array($subvalue,$active_sub_key);
                $insertSubArray[$subvalue]['categories_id'] = $id;
                $sub_id += 1;
            }
            $id += 1;
        }

        DB::table('listing_categories')->insert($insertArray);
        DB::table('listing_sub_categories')->insert($insertSubArray);

    }
}
