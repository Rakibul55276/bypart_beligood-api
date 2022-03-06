<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use Carbon\Carbon;

class HighlightListingUpdateAtDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'highlightlistingmove:top';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will remove highlighted listing if expired';

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
        $listingIds = Listing::where('listing_status', 'published')->where('is_hightlight', 1)
           ->where('highlight_end_date', '<', $currentDate)->pluck('id');

        foreach ($listingIds as $id) {
            Listing::where('id', $id)->update(['is_hightlight' => false]);
        }

        return 0;
    }
}
