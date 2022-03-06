<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingAuctionBid;
use App\Models\Listing\ListingAuctionBidAuto;
use App\Models\User;
use App\Events\AuctionBidUpdateEvent;
use App\Jobs\AutoBid as AutoBidJob;

class AutoBid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:bid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check each active auction and run auto bid for user every minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /**
         * Logic move to autobid job, so manage it one place only
         */
        $active_auction_listing_ids = Listing::where('listing_status', 'published')->where('listing_type', 'auction')->pluck('id');
        if (count($active_auction_listing_ids) > 0) {
            foreach ($active_auction_listing_ids as $listing_id) {
                dispatch(new AutoBidJob($listing_id));
            }
        }
    }
    // /**
    //  * Execute the console command.
    //  *
    //  * @return int
    //  */
    // public function handle_old()
    // {
    //     /**
    //      * This is to run against all active listing
    //      * New Step addtion to below:
    //      *  1. If there is multiple autobidder, we alwasy find second largest one
    //      * Step:
    //      *  1. Get all active auction list id
    //      *  2. Check id not empty, if empty do nothing
    //      *  3. If id exist, loop with foreach id and then following
    //      *      1. Get current bid amount for this listing and also get starting price
    //      *      2. If current bid is empty, set starting price as current bid amount
    //      *      3. If current bid amount exists, get highest bidder user_id
    //      *      4. Also get other user_id if they participated in this auction as auto bid and not highest bidder
    //      *          1. Now if this users ids is not empty loop again
    //      *          2. Get user max auto bid amount
    //      *          3. Get current bid amount
    //      *          4. Decide current bid amount for this user and inser his auto bid
    //      *      -> Sent email if outbid
    //      *  Some condition:
    //      *      1. Bid amount should not be same for any user at any time
    //      */
    //     $active_auction_listing_ids = Listing::where('listing_status', 'published')->where('listing_type', 'auction')->pluck('id');
    //     if (count($active_auction_listing_ids) > 0) {
    //         foreach ($active_auction_listing_ids as $listing_id) {

    //             $current_bid_amount = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
    //             $currentbid = $current_bid_amount ? $current_bid_amount : Listing::where('id', $listing_id)->value('starting_price');
    //             //Get current highest bidder so that we dont increment the price
    //             $highest_bidder = ListingAuctionBid::where('listing_id', $listing_id)->where('bid_amount', $currentbid)->get()->pluck('user_id');

    //             $isMoreThanOneAutoBidder = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('max_auto_bid_amount', '>=', $currentbid)->count();

    //             if ($isMoreThanOneAutoBidder > 1) {
    //                 $secondLargetBidAmount = ListingAuctionBidAuto::where('listing_id', $listing_id)
    //                     ->orderBy('max_auto_bid_amount', 'desc')->offset(1)->limit(1)->first();
    //                 $currentbid = $secondLargetBidAmount->max_auto_bid_amount;
    //                 $user_id = ListingAuctionBidAuto::where('listing_id', $listing_id)
    //                     ->orderBy('max_auto_bid_amount', 'desc')->limit(1)->pluck('user_id')->first();

    //                 //$usrMaxBidAmount = ListingAuctionBidAuto::where('user_id', $user_id)->where('listing_id', $listing_id)->value('max_auto_bid_amount');
    //                 //We get current bid amount again since, this could change again
    //                 $updatedCurrentbidAmount = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');

    //                 $currentbidAmount = $updatedCurrentbidAmount + 100;
    //                 //$currentbidAmount = $currentbidAmount > $usrMaxBidAmount ? $usrMaxBidAmount : $currentbidAmount;

    //                 ListingAuctionBid::create([
    //                     "listing_id" => $listing_id,
    //                     "bid_amount" => $currentbidAmount,
    //                     "user_id" => $user_id,
    //                 ]);
    //                 event(new AuctionBidUpdateEvent($listing_id));
    //             } else {
    //                 $listof_other_bidder = ListingAuctionBidAuto::where('listing_id', $listing_id)
    //                     ->where('is_auto_bid_enable', 1)->where('max_auto_bid_amount', '>', $currentbid)->whereNotIn('user_id', $highest_bidder)->get()->pluck('user_id');

    //                 if (count($listof_other_bidder) > 0) {
    //                     foreach ($listof_other_bidder as $user_id) {
    //                         $usrMaxBidAmount = ListingAuctionBidAuto::where('user_id', $user_id)->where('listing_id', $listing_id)->value('max_auto_bid_amount');
    //                         //We get current bid amount again since, this could change again
    //                         $updatedCurrentbidAmount = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');

    //                         $currentbidAmount = $updatedCurrentbidAmount + 100;
    //                         $currentbidAmount = $currentbidAmount > $usrMaxBidAmount ? $usrMaxBidAmount : $currentbidAmount;

    //                         ListingAuctionBid::create([
    //                             "listing_id" => $listing_id,
    //                             "bid_amount" => $currentbidAmount,
    //                             "user_id" => $user_id,
    //                         ]);
    //                     }
    //                     // Send event to FE
    //                     event(new AuctionBidUpdateEvent($listing_id));
    //                 }
    //             }

    //             // dd($highest_bidder);




    //             /**
    //              * Dispatch Email event if outbid anyone
    //              * Also need to make sure, we already sent them email by checking not empty users
    //              * - Get if anyone request email them incase outbid
    //              * - Send email only once to avoid extra cost
    //              */
    //             $currentbidNow = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
    //             //This is to avoid illegal operator error when comparing with not data in database
    //             $isExistEmailReminder = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('is_email_reminder_on', 1)->exists();
    //             if ($isExistEmailReminder) {
    //                 $ids = ListingAuctionBidAuto::where('listing_id', $listing_id)
    //                     ->where('is_email_reminder_on', 1)->where('max_auto_bid_amount', '<', $currentbidNow)->get()->pluck('user_id');

    //                 //We get highest bidder, so that we donot send email notification
    //                 $highest_bidder = ListingAuctionBid::where('listing_id', $listing_id)->orderBy('bid_amount', 'desc')->first();
    //                 $highest_bidder_id = $highest_bidder !== null ? $highest_bidder->user_id : 0;

    //                 if (count($ids) !== 0) {
    //                     $listing_link = config('bypart.frontend_url') . 'pages/buy/cars/' . $listing_id;

    //                     foreach ($ids as $id) {
    //                         if ($id !== $highest_bidder_id) {
    //                             $email = User::select('first_name', 'email')->where('id', $id)->first();
    //                             //Marking is_email_reminder_on to false since we already send them email
    //                             $matchThese = ['listing_id' => $listing_id, 'user_id' => $id];
    //                             ListingAuctionBidAuto::updateOrCreate($matchThese, [
    //                                 "listing_id" => $listing_id,
    //                                 "user_id" => $id,
    //                                 "is_email_reminder_on" => false
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }
}
