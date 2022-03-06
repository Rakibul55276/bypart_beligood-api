<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryArray = array("Accessories & Electronics", "Performance & Car Parts", "Grooming & Car Care", "Maintenance & Repair","Car Sale & Rental","General Information");

        foreach ($categoryArray as $cat) {
            DB::table('directory_category')->insert([
                'category_name' => $cat,
            ]);
        }
    }
}
