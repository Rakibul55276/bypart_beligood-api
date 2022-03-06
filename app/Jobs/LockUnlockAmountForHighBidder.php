<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing\ListingAuctionBid;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use App\Models\Listing\Listing;
use App\Models\Payment\UserPointLock;

class LockUnlockAmountForHighBidder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    protected $listing_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listing_id)
    {
        $this->listing_id = $listing_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $listing_id = $this->listing_id;

        //Release high bidder amount
        $action = 'high_bidder_lock_amount';
        $user_ids_lock_amount = UserPointLock::where('listing_id', $listing_id)->where('point_lock_type',  $action)->pluck('user_id');
        foreach ($user_ids_lock_amount as $user_id) {
            app(PointDeductionController::class)->release_lock_point($user_id, $action, $listing_id, false);
        }

        //Now lock amount for high bidder
        $currentbid = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
        if ($currentbid !== null) {
            $high_bidder_id = ListingAuctionBid::where('listing_id', $listing_id)->where('bid_amount', $currentbid)->value('user_id');
            $ad_title = Listing::where('id', $listing_id)->value('ad_title');

            $lock_description = 'Point lock since you are top bidder for ' . $ad_title;
            app(PointDeductionController::class)->lock_point_for_user($high_bidder_id, $action, $lock_description, $listing_id, false);
        }

        return 0;
    }
}
