<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use Carbon\Carbon;
use App\Models\Listing\ListingBumpIds;

class BumpAdsListingUpdatedAtDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump:ads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To bump ads every miorning 10am';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         $currentDate = Carbon::now()->toDateTimeString();
         $listingIds = ListingBumpIds::where('bump_end_date', '>', $currentDate)->pluck('listing_id');

         foreach ($listingIds as $id) {
             Listing::where('id', $id)->update(['updated_at' => now()]);
         }
        return 0;
    }
}
