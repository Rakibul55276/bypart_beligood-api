<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Listing\ListingAuctionBid;
use App\Models\Listing\ListingAuctionBidAuto;
use App\Events\AuctionBidUpdateEvent;
use App\Models\User;
use App\Models\Listing\Listing;
use Carbon\Carbon;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Jobs\LockUnlockAmountForHighBidder;

class AutoBid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    
    public $listing_id;
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
        $current_bid_amount = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
        $currentbid = $current_bid_amount ? $current_bid_amount : Listing::where('id', $listing_id)->value('starting_price');

        //First we will check if there is multiple autobidder and we increase accoringly
        $no_of_auto_bidder = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('max_auto_bid_amount', '>', $currentbid)->count();
        if ($no_of_auto_bidder > 1) {
            $secondLargetBidAmount = ListingAuctionBidAuto::where('listing_id', $listing_id)
                ->orderBy('max_auto_bid_amount', 'desc')->offset(1)->limit(1)->first();
            $currentbid = $secondLargetBidAmount->max_auto_bid_amount;
            /**
             * Check if multiple user have this bid amount and this is highest amount
             */
            $max_amount_for_auto_bid = ListingAuctionBidAuto::where('listing_id', $listing_id)->max('max_auto_bid_amount');
            if ($currentbid === $max_amount_for_auto_bid) {
                $user_ids = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('max_auto_bid_amount', $currentbid)->pluck('user_id');
                $bid_amount_to_insert = $max_amount_for_auto_bid;
            } else {
                $user_ids = ListingAuctionBidAuto::where('listing_id', $listing_id)
                    ->orderBy('max_auto_bid_amount', 'desc')->limit(1)->pluck('user_id');
                $bid_amount_to_insert = $currentbid + 100;
            }

            /**
             * Check against any other bidder, which auto bid amount is less than current bid_amount_to_insert and set their bid at their auto bid amount.
             */
            $other_users_which_auto_bid_amount_less_than_bid_amount_to_insert = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('max_auto_bid_amount', '!=', 0)->where('max_auto_bid_amount', '<', $bid_amount_to_insert)->pluck('user_id');
            foreach ($other_users_which_auto_bid_amount_less_than_bid_amount_to_insert as $user_id) {
                $user_max_bid_amount = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('user_id', $user_id)->value('max_auto_bid_amount');
                $isThisBidExistForThisUser = ListingAuctionBid::where('listing_id', $listing_id)->where('user_id', $user_id)->where('bid_amount', $user_max_bid_amount)->exists();
                if (!$isThisBidExistForThisUser) {
                    ListingAuctionBid::create([
                        "listing_id" => $listing_id,
                        "bid_amount" => $user_max_bid_amount,
                        "user_id" => $user_id,
                    ]);
                }
            }
            /**
             * End of outbidder bid amount
             */

            /**
             * After extract users_id lets loop and insert and also validate again
             * - Aug-30 update logic, so that two user does not have same amount
             * - So, we added if condition
             * - If there is more than one user_ids mean both user have same amount as autobid
             */

            foreach ($user_ids as $user_id) {
                /**
                 * Check against max_bid_amount that they set enough so that we can insert bid
                 */
                $user_max_bid_amount = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('user_id', $user_id)->value('max_auto_bid_amount');
                $current_bid_after_one_loop = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');

                if ($bid_amount_to_insert <= $user_max_bid_amount && $current_bid_after_one_loop != $user_max_bid_amount) {
                    ListingAuctionBid::create([
                        "listing_id" => $listing_id,
                        "bid_amount" => $bid_amount_to_insert,
                        "user_id" => $user_id,
                    ]);
                } else if ($current_bid_after_one_loop == $user_max_bid_amount) {
                    ListingAuctionBid::create([
                        "listing_id" => $listing_id,
                        "bid_amount" => $current_bid_after_one_loop - 100,
                        "user_id" => $user_id,
                    ]);
                } else {
                    ListingAuctionBid::create([
                        "listing_id" => $listing_id,
                        "bid_amount" =>  $user_max_bid_amount,
                        "user_id" => $user_id,
                    ]);
                }
                //Send event to FE
                event(new AuctionBidUpdateEvent($listing_id));
            }
        } else {
            //Get current highest bidder so that we dont increment the price
            $highest_bidder = ListingAuctionBid::where('listing_id', $listing_id)->where('bid_amount', $currentbid)->get()->pluck('user_id');

            $listof_other_bidder = ListingAuctionBidAuto::where('listing_id', $listing_id)
                ->where('is_auto_bid_enable', 1)->where('max_auto_bid_amount', '>', $currentbid)->whereNotIn('user_id', $highest_bidder)->get()->pluck('user_id');
            if (count($listof_other_bidder) > 0) {
                foreach ($listof_other_bidder as $user_id) {
                    $usrMaxBidAmount = ListingAuctionBidAuto::where('user_id', $user_id)->where('listing_id', $listing_id)->value('max_auto_bid_amount');
                    $starting_price = Listing::where('id', $listing_id)->value('starting_price');
                    //We get current bid amount again since, this could change again
                    $updatedCurrentbidAmount = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');

                    $currentbidAmount = $updatedCurrentbidAmount !== null ? $updatedCurrentbidAmount : $starting_price;
                    $currentbidAmount = $currentbidAmount + 100;
                    $currentbidAmount = $currentbidAmount > $usrMaxBidAmount ? $usrMaxBidAmount : $currentbidAmount;

                    ListingAuctionBid::create([
                        "listing_id" => $listing_id,
                        "bid_amount" => $currentbidAmount,
                        "user_id" => $user_id,
                    ]);
                }
                // Send event to FE
                event(new AuctionBidUpdateEvent($listing_id));
            }
        }

        /**
         * Dispatch Email event if outbid anyone
         * Also need to make sure, we already sent them email by checking not empty users
         * - Get if anyone request email them incase outbid
         * - Send email only once to avoid extra cost
         */
        $currentbidNow = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
        //This is to avoid illegal operator error when comparing with not data in database
        //Following to avoid null
        $currentbidNow = $currentbidNow === null ? 0 : $currentbidNow;

        //We get highest bidder, so that we donot send email notification
        $highest_bidder = ListingAuctionBid::where('listing_id', $listing_id)->orderBy('bid_amount', 'desc')->first();
        $highest_bidder_id = $highest_bidder !== null ? $highest_bidder->user_id : 0;

        //Get ids without higest bidder
        $ids = ListingAuctionBidAuto::where('listing_id', $listing_id)
            ->where('is_email_reminder_on', 1)->where('max_auto_bid_amount', '<=', $currentbidNow)
            ->where('user_id', '!=', $highest_bidder_id)->get()->pluck('user_id');

        if (count($ids) > 0) {
            $listing_link = config('bypart.frontend_url') . 'pages/buy/cars/' . $listing_id;

            foreach ($ids as $user_id) {
                $email = User::select('first_name', 'email')->where('id', $user_id)->get();
                dispatch(new SendEmailWhenOutBid($email[0]->email, $email[0]->first_name, $listing_link, $listing_id, $user_id))->delay(Carbon::now()->addSeconds(10));;
                //Marking is_email_reminder_on to false since we already send them email
                $matchThese = ['listing_id' => $listing_id, 'user_id' => $user_id];
                ListingAuctionBidAuto::updateOrCreate($matchThese, [
                    "listing_id" => $listing_id,
                    "user_id" => $user_id,
                    "is_email_reminder_on" => false
                ]);
            }
        }

        /**
         * Call Notification Global Saving API Start
         */
        $user_ids = ListingAuctionBidAuto::where('listing_id', $listing_id)->where('is_out_bid_notification_sent', 0)
            ->where('max_auto_bid_amount', '<=', $currentbid)->where('user_id', '!=', $highest_bidder_id)->get()->pluck('user_id');

        if (count($user_ids) > 0) {
            $owner_of_the_listing = Listing::where('id', $listing_id)->value('user_id');
            $ad_title = Listing::where('id', $listing_id)->value('ad_title');

            $notification_header = 'You are outbid';
            $notification_content = "You are outbid on " . $ad_title;
            $notification_type = 'outbid';
            $sender_id = $owner_of_the_listing;

            $url = config('bypart.frontend_url') . 'pages/buy/cars/' . $listing_id;

            app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $user_ids, $listing_id);

            foreach ($user_ids as $user_id) {
                //Since we already sent the notification, lets set to false
                $matchThese = ['listing_id' => $listing_id, 'user_id' => $user_id];
                ListingAuctionBidAuto::updateOrCreate($matchThese, [
                    "listing_id" => $listing_id,
                    "user_id" => $user_id,
                    "is_out_bid_notification_sent" => true
                ]);
            }
        }
        //Call Notification Global Saving API End

        //Dispatch this event to lock unlock points
        dispatch(new LockUnlockAmountForHighBidder($listing_id));

        return 0;
    }
}
