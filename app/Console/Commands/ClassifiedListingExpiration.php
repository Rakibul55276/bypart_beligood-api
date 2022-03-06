<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use Carbon\Carbon;

class ClassifiedListingExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classifiedlisting:expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'By this command, we can mark any classified ads as expired if created date of listing > 60 days';

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
        //Filter which auction is expired
        $currentDate = Carbon::now()->subDays(60);
        $listingIds = Listing::where('listing_status', 'published')->where('listing_type', 'classified')
            ->where('created_at', '<', $currentDate)->update([
                'listing_status' => 'expired'
            ]);
    }
}
