<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use Carbon\Carbon;
use App\Models\Listing\ListingBumpIds;

class FeatureListingUpdateAtDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featurelistingmove:top';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will move feature listing in top result if it is paid by the user';

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

        //Check if any feature expired and set is_feature to 0
        $listingIds = Listing::where('listing_status', 'published')->where('is_feature', 1)
           ->where('feature_end_date', '<', $currentDate)->pluck('id')->update(['is_feature' => false]);

        return 0;
    }
}
