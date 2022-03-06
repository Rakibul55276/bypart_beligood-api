<?php

namespace Database\Seeders;

use App\Models\Listing;
use Illuminate\Database\Seeder;
use bfinlay\SpreadsheetSeeder\SpreadsheetSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Calling from speardsheet
        //$this->call([SpreadsheetSeeder::class]);
        // Other
        //$this->call(UserSeeder::class);

        $this->call(DirectoryCategorySeeder::class);
        $this->call(DirectorySubCategorySeeder::class);
        //$this->call(CompanySeeder::class);

        //$this->call(ListingSeeder::class);

        $this->call(PointSystemSettingSeeder::class);
        //$this->call(ForumSeeder::class);
        $this->call(ForumCategorySeeder::class);
        $this->call(ListingCategorySeeder::class);
    }
}
