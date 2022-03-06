<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing\Listing;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Listing\ListingAuctionBid;
use App\Jobs\SendEmailToAuctionWinner;
use App\Jobs\SendEmailToAuctionParticipants;
use App\Jobs\SendEmailToSellerWhenVehicleSold;
use App\Models\Listing\ListingUserActivity;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use App\Models\Payment\UserPointLock;

class CloseAuction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auction:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check every minute for auction closing time to decide winner';

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
        $currentDate = Carbon::now()->toDateTimeString();
        $listingIds = Listing::where('listing_status', 'published')->where('listing_type', 'auction')
            ->where('end_date', '<', $currentDate)->pluck('id');

        foreach ($listingIds as $listingid) {
            $getReservePrice = Listing::where('id', $listingid)->value('reserve_price');
            $decideWinnerAmount = ListingAuctionBid::where('listing_id', $listingid)->max('bid_amount');

            //If decideWinner return empty, that means no winner, no loser or no bid perform
            if ($decideWinnerAmount === null) {
                Listing::where('id', $listingid)->update([
                    'listing_status' => 'expired'
                ]);
            }

            // Mark unsold if auction does not meet reserved price and send email to all participants
            if ($decideWinnerAmount !== null && $getReservePrice > $decideWinnerAmount) {
                Listing::where('id', $listingid)->update([
                    'listing_status' => 'unsold'
                ]);

                /**
                 * Since auction end and item did not sold, release the point only applicable for private user
                 */
                $sellerId = Listing::where('id', $listingid)->value('user_id');
                $user_type = User::where('id', $sellerId)->value('user_type');
                if ($user_type == 'user') {
                    $action_name_for_release = 'new_auction_listing_lock_amount';
                    app(PointDeductionController::class)->release_lock_point($sellerId, $action_name_for_release, $listingid);
                }
                /**
                 * End release point
                 */

                $allParticipantsUserIds = ListingAuctionBid::where('listing_id', $listingid)->pluck('user_id');
                //Update all lisit_user_activity table and dispatch email for each
                foreach ($allParticipantsUserIds as $user_id) {
                    $matchThese = ['listing_id' => $listingid, 'user_id' => $user_id];
                    ListingUserActivity::updateOrCreate($matchThese, [
                        "listing_id" => $listingid,
                        "user_id" => $user_id,
                        "is_lost" => 1
                    ]);
                    dispatch(new SendEmailToAuctionParticipants($user_id, $listingid, "Unsold, reserve did not met"));
                }
            }

            // If max_bid reserve met, make highest biddder as winner, send email and also send email to other participants
            // Also send to seller
            if ($decideWinnerAmount !== null && $getReservePrice <= $decideWinnerAmount) {
                Listing::where('id', $listingid)->update([
                    'listing_status' => 'sold'
                ]);
                //Now decide winner, but only pick first one and send him email. also update user acitivituy table
                $winnerId = ListingAuctionBid::where('listing_id', $listingid)->where('bid_amount', '=', $decideWinnerAmount)->orderBy('created_at', 'desc')->pluck('user_id')->first();
                $matchThese = ['listing_id' => $listingid, 'user_id' => $winnerId];
                ListingUserActivity::updateOrCreate($matchThese, [
                    "listing_id" => $listingid,
                    "user_id" => $winnerId,
                    "is_won" => 1
                ]);
                dispatch(new SendEmailToAuctionWinner($winnerId, $listingid));

                $buyer_f_name = User::where('id', $winnerId)->value('first_name');
                $buyer_l_name = User::where('id', $winnerId)->value('last_name');
                $buyer_name = $buyer_f_name . ' ' . $buyer_l_name;

                //Dispatch and send email to seller
                $sellerId = Listing::where('id', $listingid)->value('user_id');
                dispatch(new SendEmailToSellerWhenVehicleSold($sellerId, $listingid, $decideWinnerAmount, $buyer_name));

                /**
                 * Now deduct success fee for listing seller,
                 * - and also release if any amount lock for this listing
                 */
                $action_name_for_release = 'new_auction_listing_lock_amount';
                $action_name = 'success_fee';
                $purchase_info = 'Your listing sold successfully with high bid amount';
                app(PointDeductionController::class)->deduct_point_from_user($sellerId, $action_name, $listingid, $purchase_info);
                app(PointDeductionController::class)->release_lock_point($sellerId, $action_name_for_release, $listingid);
                /**
                 * End success fee
                 */
                //Now also delete incase any amount was lock due to high bid
                $action_high_bidder = 'high_bidder_lock_amount';
                $user_ids_lock_amount = UserPointLock::where('listing_id', $this->listing_id)->where('point_lock_type',  $action_high_bidder)->pluck('user_id');
                foreach ($user_ids_lock_amount as $user_id) {
                    app(PointDeductionController::class)->release_lock_point($user_id, $action_high_bidder, $this->listing_id);
                }
                /**
                 * Now deduct success fee for the buyer,
                 */
                $ad_title = Listing::where('id', $this->listing_id)->value('ad_title');
                $buyer_action_name = 'auction_winner_success_fee';
                $purchase_info = 'Your purchase was successful as high bidder for ' . $ad_title;
                $buyer_id = $winnerId;
                app(PointDeductionController::class)->deduct_point_from_user($buyer_id, $buyer_action_name,  $this->listing_id, $purchase_info);
                /**
                 * End deduction from buyer
                 */

                //Update all lisit_user_activity table and dispatch email for each
                $getOtherParticipantsIds = ListingAuctionBid::where('listing_id', $listingid)->where('user_id', '!=', $winnerId)->pluck('user_id');
                $bidDeatils = "Sold by highest bid amount " . $decideWinnerAmount;
                foreach ($getOtherParticipantsIds as $user_id) {
                    $matchThese = ['listing_id' => $listingid, 'user_id' => $user_id];
                    ListingUserActivity::updateOrCreate($matchThese, [
                        "listing_id" => $listingid,
                        "user_id" => $user_id,
                        "is_lost" => 1
                    ]);
                    dispatch(new SendEmailToAuctionParticipants($user_id, $listingid, $bidDeatils));
                }

                /**
                 * Call Notification Global Saving API Start
                 */
                $notification_header = 'Congratulations! Auction winner';
                $notification_content = "Congratulations! you are the winner of the auction product.";
                $notification_type = 'auction-winner';
                $sender_id = $sellerId;
                $user_ids = array($winnerId);
                $url = config('bypart.frontend_url') . 'pages/buy/cars/' . $listingid;

                app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $user_ids, $listingid);
                /**
                 * Call Notification Global Saving API End
                 */
            }
        }
        return 0;
    }
}
