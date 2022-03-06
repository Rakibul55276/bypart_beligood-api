<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiController;
use App\Models\Listing\ListingAuctionBid;
use App\Models\Listing\ListingAuctionBidAuto;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingUserActivity;
use App\Events\AuctionBidUpdateEvent;
use Illuminate\Support\Facades\DB;
use App\Jobs\AutoBid;
use Carbon\Carbon;
use App\Models\User;
use App\Jobs\SendEmailPurchaseConfirmation;
use App\Jobs\SendEmailToSellerWhenVehicleSold;
use App\Jobs\SendEmailToAuctionParticipants;
use App\Jobs\LockUnlockAmountForHighBidder;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use App\Models\Payment\UserPointLock;

class CarAuctionController extends ApiController
{
    /**
     * TODO: Validation against listing data such as minimum bid price
     */
    public function placeBid(Request $request)
    {
        $user = Auth::user();
        $is_email_reminder_on = $request->has('is_email_reminder_on') ? $request->is_email_reminder_on : 0;
        $is_auto_bid_enable = $request->has('is_auto_bid_enable') ? $request->is_auto_bid_enable : 0;

        $validator = Validator::make($request->all(), [
            'listing_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        /**
         * Check if user have enough point to place bid or autobid
         *  - User required minimum 200 point, and can set with `high_bidder_lock_amount`
         *  - all kind of user type
         */
        $action = 'high_bidder_lock_amount';
        if (!app(PointDeductionController::class)->validate_user_point($user->id, $action)) {
            $point_required = app(PointDeductionController::class)->deduct_amount($user->id, $action);
            return $this->errorResponse('Not enough points to perform bidding, You required minimum ' . $point_required, 422,);
        }

        /**
         * Check if auction date is end
         */
        $currentDate = Carbon::now()->toDateTimeString();
        $isExpired = Listing::where('id', $request->listing_id)->where('listing_type', 'auction')
            ->where('end_date', '<', $currentDate)->exists();
        if ($isExpired) {
            return $this->errorResponse('Auction has been ended! You are not allow to place bid on close auction', 422);
        }
        /**
         * If user own this listing, do not allow to submit bid
         */
        $isOwnerisBidding = Listing::where('id', $request->input('listing_id'))->where('user_id', $user->id)->exists();
        if ($isOwnerisBidding) {
            return $this->errorResponse('Sorry, you are not allowed to submit bid for your own listing', 422);
        }

        // Some other manual validation
        if ($is_auto_bid_enable && !$request->has('max_auto_bid_amount')) {
            return $this->errorResponse('Please spcify max auto bid amount', 422);
        }
        /**
         * If it is autobid execute following
         * - Buyer set autobid = rm1000
         * - Current price immediately change to rm600 (buyer B is highest bidder)
         */
        $isAutoBidSet = null;
        if ($request->has('is_email_reminder_on') || $request->has('is_auto_bid_enable')) {
            $max_auto_bid_amount = $request->has('max_auto_bid_amount') ? $request->max_auto_bid_amount : 0;
            $matchThese = ['listing_id' => $request->listing_id, 'user_id' => $user->id];
            $isAutoBidSet = ListingAuctionBidAuto::updateOrCreate($matchThese, [
                "listing_id" => $request->listing_id,
                "user_id" => $user->id,
                "is_email_reminder_on" => $is_email_reminder_on,
                "is_auto_bid_enable" => $is_auto_bid_enable,
                "max_auto_bid_amount" => $max_auto_bid_amount,
                "is_out_bid_notification_sent" => false,
            ]);
        }

        /**
         * This part only if user place bid
         */
        //If bid amount is less than current amount, send error message
        if ($request->has('bid_amount') && $request->bid_amount !== null) {
            $isBidAmountLessThanCurrentBid = ListingAuctionBid::where('listing_id', $request->listing_id)
                ->where('bid_amount', '>=', $request->bid_amount)->first();
            if ($isBidAmountLessThanCurrentBid) {
                return $this->errorResponse("Please choose higher amount than current bid amount", 422);
            }

            $create_bid = ListingAuctionBid::create([
                "listing_id" => $request->listing_id,
                "bid_amount" => $request->bid_amount,
                "user_id" => $user->id,
            ]);
            if ($create_bid) {
                //Since user place bid again, so we reset there notification preferences
                $matchThese = ['listing_id' => $request->listing_id, 'user_id' => $user->id];
                ListingAuctionBidAuto::updateOrCreate($matchThese, [
                    "listing_id" => $request->listing_id,
                    "user_id" => $user->id,
                    "is_out_bid_notification_sent" => false
                ]);
                //Trigger event here
                event(new AuctionBidUpdateEvent($request->listing_id));

                /**
                 * CLock and unlock point after this transaction
                 */
                //dispatch(new LockUnlockAmountForHighBidder($request->listing_id));

                dispatch(new AutoBid($request->listing_id))->delay(Carbon::now()->addSeconds(5));

                return $this->successResponse(true, "Successfully submitted your bid");
            }
        }

        if ($isAutoBidSet !== null) {
            // Trigger when autobid set, so we can bid for users
            event(new AuctionBidUpdateEvent($request->listing_id));

            dispatch(new AutoBid($request->listing_id))->delay(Carbon::now()->addSeconds(5));
            return $this->successResponse(true, "Successfully set your bid preferences");
        }

        return $this->errorResponse("Whoops! something wrong when you submit your bid");
    }

    public function cancelAutoBid(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $matchThese = ['listing_id' => $request->listing_id, 'user_id' => $user->id];
        ListingAuctionBidAuto::updateOrCreate($matchThese, [
            "listing_id" => $request->listing_id,
            "user_id" => $user->id,
            "is_auto_bid_enable" => false,
            "max_auto_bid_amount" => 0,
        ]);

        return $this->successResponse(true, "Successfully cancel your auto bid");
    }

    public function getAllBidListingWithUserById($listing_id)
    {
        $user = Auth::user();
        $data = ListingAuctionBid::where('listing_id', $listing_id)
            ->join('users', 'users.id', '=', 'listing_auction_bid.user_id')
            ->orderBy('listing_auction_bid.bid_amount', 'desc')
            ->get(['listing_auction_bid.id as auction_bid_id', 'listing_auction_bid.listing_id', 'users.id as user_id', 'users.first_name', 'users.last_name', 'listing_auction_bid.bid_amount', 'listing_auction_bid.created_at']);
        /**
         * If user have any autobid set also send the amount
         * And append this only in the first result and data length is > 0
         * - This will display to user with cancle button by FE
         */
        $auto_bid_amount_if_any = ListingAuctionBidAuto::where('user_id', $user->id)->where('listing_id', $listing_id)->value('max_auto_bid_amount');
        $auto_bid_amount = $auto_bid_amount_if_any === null ? 0 : $auto_bid_amount_if_any;
        if (count($data) > 0) {
            $data[0]['auto_bid_amount'] = $auto_bid_amount;
        }

        /**
         * If this listing bough with buy now price then sent buy now price and user details
         */
        $is_bought = ListingUserActivity::where('listing_id', $listing_id)->where('is_bought', 1)->exists();

        if ($is_bought) {
            $get_buyer = ListingUserActivity::select('user_id', 'buy_time')->where('listing_id', $listing_id)->where('is_bought', 1)->get();

            $buy_now_price = Listing::where('id', $listing_id)->value('buy_now_price');
            $user_data = User::select('id as user_id', 'first_name', 'last_name')->where('id', $get_buyer[0]->user_id)->first();
            $user_data['buy_now_price'] = $buy_now_price;
            $user_data['buy_time'] = $get_buyer[0]->buy_time;

            if (count($data) > 0) {
                $data[0]['sold_with_buy_now'] = $user_data;
            } else {
                $data['sold_with_buy_now'] = $user_data;
            }
        }

        return $this->successResponse($data);
    }

    public function updateAutoBid($listing_id)
    {
        // Dispatching auto bid
        dispatch(new AutoBid($listing_id, $this->getAllBidListingWithUserById($listing_id)))->delay(Carbon::now()->addSeconds(5));
        return $this->successResponse('done');
    }

    /**
     * TODO: Check if user have enough point to purchase
     * Also improve error message why not available to purchase
     */
    public function buyNow(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        $listing_id = $request->listing_id;
        $currentbid = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
        $currentbid = $currentbid === null ? 0 : $currentbid;

        $isItAllowedToBuy = Listing::where('id', $listing_id)->where('listing_status', 'published')
            ->where('buy_now_price', '>', $currentbid)->exists();

        if (!$isItAllowedToBuy) {
            return $this->errorResponse('This is not available for purchase', 422);
        }

        /**
         * Check if user have enough point to purchase
        */
        $action = 'auction_winner_success_fee';
        if (!app(PointDeductionController::class)->validate_user_point($user->id, $action)) {
            $point_required = app(PointDeductionController::class)->deduct_amount($user->id, $action);
            return $this->errorResponse('Not enough points to purchase this listing, You required minimum ' . $point_required, 422,);
        }

        $generateReference = sha1(now());
        $matchThese = ['listing_id' => $listing_id, 'user_id' => $user->id];
        $listingUserActivityTable = ListingUserActivity::updateOrCreate($matchThese, [
            "listing_id" => $listing_id,
            "user_id" => $user->id,
            "is_bought" => 1,
            "buy_now_reference" => $generateReference,
            "buy_time" => now()
        ]);

        $sellerId = Listing::where('id', $listing_id)->value('user_id');
        if ($listingUserActivityTable) {
            Listing::where('id', $listing_id)->update([
                'listing_status' => 'sold',
                'end_date' => now()
            ]);

            /**
             * Now deduct success fee for listing seller,
             * - and also release if any amount lock for this listing
             */
            $action_name_for_release = 'new_auction_listing_lock_amount';
            $action_name = 'success_fee';
            $purchase_info = 'Your listing sold successfully over buy now price';
            app(PointDeductionController::class)->release_lock_point($sellerId, $action_name_for_release, $listing_id);
            app(PointDeductionController::class)->deduct_point_from_user($sellerId, $action_name, $listing_id, $purchase_info);
            /**
             * End success fee
             */

            //Now also delete incase any amount was lock due to high bid
            $action_high_bidder = 'high_bidder_lock_amount';
            $user_ids_lock_amount = UserPointLock::where('listing_id', $listing_id)->where('point_lock_type',  $action_high_bidder)->pluck('user_id');
            foreach ($user_ids_lock_amount as $user_id) {
                app(PointDeductionController::class)->release_lock_point($user_id, $action_high_bidder, $listing_id);
            }
            /**
             * Now deduct success fee for the buyer,
             */
            $ad_title = Listing::where('id', $listing_id)->value('ad_title');
            $buyer_action_name = 'auction_winner_success_fee';
            $purchase_info = 'Your purchase was successful over buy now price for ' . $ad_title;
            $buyer_id = $user->id;
            app(PointDeductionController::class)->deduct_point_from_user($buyer_id, $buyer_action_name, $listing_id, $purchase_info);
            /**
             * End deduction from buyer
             */

            //Check if update other user if they participate in this listing, mark as lost
            $bidder_id = ListingAuctionBid::where('listing_id', $listing_id)->distinct()->pluck('user_id');
            $buy_now_price = Listing::where('id', $listing_id)->value('buy_now_price');
            foreach ($bidder_id as $userid) {
                if ($user->id !== $userid) {
                    $matchThese = ['listing_id' => $request->input('listing_id'), 'user_id' => $userid];
                    ListingUserActivity::updateOrCreate($matchThese, [
                        "listing_id" => $listing_id,
                        "user_id" => $userid,
                        "is_lost" => 1
                    ]);
                    //Also send them email that they lose and remark as buy now
                    dispatch(new SendEmailToAuctionParticipants($userid, $listing_id, "Sold with Buy now price, amount is " . $buy_now_price));
                }
            }
        }
        $sellerInfo = User::where('id', $sellerId)->get(['first_name', 'last_name', 'email', 'mobile_no', 'address', 'city', 'state']);
        $sellerInfo[0]['buy_now_reference'] = $generateReference;

        /**
         * Send email for purchaese confirmation
         */
        $date = Carbon::now();
        $listing_details = Listing::select('buy_now_price', 'car_make_name', 'model', 'car_body_type', 'manufacture_year')->where('id', $listing_id)->get();
        $buyer_email = $user->email;
        $buyer_name = $user->first_name . ' ' . $user->last_name;
        $vehicle_model = $listing_details[0]->car_make_name . ' ' . $listing_details[0]->model . ' ' . $listing_details[0]->car_body_type . ' ' . $listing_details[0]->manufacture_year;
        $purchase_time = $date->toDayDateTimeString();
        $reference_id = $generateReference;
        $purchase_amount = $listing_details[0]->buy_now_price;

        dispatch(new SendEmailPurchaseConfirmation($buyer_email, $buyer_name, $vehicle_model, $purchase_time, $reference_id, $purchase_amount));
        //Dispatch and send email to seller
        dispatch(new SendEmailToSellerWhenVehicleSold($sellerId, $listing_id, $listing_details[0]->buy_now_price, $buyer_name));

        return $this->successResponse($sellerInfo, "Thank you for your purchase");
    }
}
