<?php

namespace App\Http\Controllers\Api\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment\UserPointDeductionHistory;
use App\Models\Payment\UserPointLock;

use Throwable;

use App\Models\Listing\ListingAuctionBid;
use App\Models\Listing\ListingAuctionBidAuto;
use App\Models\Listing\Listing;

use App\Events\BpPointUpdateEvent;
use App\Events\AuctionBidUpdateEvent;

class PointDeductionController extends ApiController
{
    /**
     * This is use in FE
     */
    public function isAllowToPerformThisAction(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'action' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422,);
        }

        $isAllow = $this->validate_user_point($user->id, $request->action);
        if ($isAllow) {
            return $this->successResponse(true);
        }

        return $this->errorResponse('Sorry, you do not have enough points to perform this action', 422,);
    }

    public function getUserPoint()
    {
        $user = Auth::user();
        $current_bp_point = User::where('id', $user->id)->value('bp_point');

        return $this->successResponse($current_bp_point);
    }

    public function getPointTransactionType()
    {
        $user_type = User::where('id', Auth::user()->id)->value('user_type');

        $user_type_in_point_system = '';
        if ($user_type === 'user') {
            $user_type_in_point_system = 'private';
        } else if ($user_type === 'dealer') {
            $user_type_in_point_system = 'dealer';
        } else if ($user_type === 'agent') {
            $user_type_in_point_system = 'broker_agent';
        }

        $pointTransactionType = DB::table('user_point_setting')
            ->where('point_category', $user_type_in_point_system)
            ->get(['id', 'transaction_type', 'deduction_point']);
        return $this->successResponse($pointTransactionType);
    }

    public function getUserPointWithType()
    {
        $user_type = User::select('user_type', 'bp_point')->where('id', Auth::user()->id)->get();

        $user_type_in_point_system = '';
        if ($user_type[0]->user_type === 'user') {
            $user_type_in_point_system = 'private';
        } else if ($user_type[0]->user_type === 'dealer') {
            $user_type_in_point_system = 'dealer';
        } else if ($user_type[0]->user_type === 'agent') {
            $user_type_in_point_system = 'broker_agent';
        }

        $pointTransactionType = DB::table('user_point_setting')
            ->where('point_category', $user_type_in_point_system)
            ->get(['id', 'transaction_type', 'deduction_point']);
        $pointTransactionType['bp_point'] = $user_type[0]->bp_point;
        return $this->successResponse($pointTransactionType);
    }

    /**
     * User deducted point
     */
    public function getUserPointHistory($limit = 10)
    {
        $user = Auth::user();
        $userPoint = UserPointDeductionHistory::select('transaction_type', 'deducted_point', 'transaction_description', 'other_details', 'listing_id', 'created_at')
            ->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate($limit);

        return $this->successResponse($userPoint);
    }
    /**
     * User lock point
     */

    public function getUserLockPoint($limit = 10)
    {
        $user = Auth::user();
        $userPoint = UserPointLock::select('point_lock_type', 'lock_point_amount', 'point_lock_description', 'listing_id', 'created_at')
            ->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate($limit);

        return $this->successResponse($userPoint);
    }
    /**
     * Call this function bp_point need to deduct and update point history table
     */
    public function deduct_point_from_user($user_id, $action, $listing_id = null, $transaction_description = null, $details = null)
    {
        $point_to_deduct = $this->deduct_amount($user_id, $action);
        //Only deduct when there is valid amount
        $update_user_point = User::where('id', $user_id)->update([
            'bp_point' => DB::raw('bp_point - ' . $point_to_deduct)
        ]);

        UserPointDeductionHistory::create([
            'user_id' => $user_id,
            'transaction_type' => $action,
            'deducted_point' => $point_to_deduct,
            'other_details' => $details,
            'transaction_description' => $transaction_description,
            'listing_id' => $listing_id,
        ]);

        //Lets notify user
        broadcast(new BpPointUpdateEvent($user_id));
    }

    public function validate_user_point($user_id, $action)
    {
        $current_bp_point = User::where('id', $user_id)->value('bp_point');
        $point_required_to_perform_this_action = $this->deduct_amount($user_id, $action);
        // dd($point_required_to_perform_this_action);
        if ($current_bp_point < $point_required_to_perform_this_action) {
            return false;
        }

        return true;
    }

    /**
     * Aginst each event check how many point should deduct
     * Also could check if user has enough point to perform this action
     */
    public function deduct_amount($user_id, $action)
    {
        $user_type = User::where('id', $user_id)->value('user_type');

        $category_type = '';
        if ($user_type === 'user') {
            $category_type = 'private';
        } else if ($user_type === 'dealer') {
            $category_type = 'dealer';
        } else if ($user_type === 'agent') {
            $category_type = 'broker_agent';
        }

        switch ($action) {
            case 'classified_listing_price':
                return $this->point_to_deduct('classified_listing_price', $category_type);
                break;
            case 'auction_listing_price':
                return $this->point_to_deduct('auction_listing_price', $category_type);
                break;
            case 'edit_fee':
                return $this->point_to_deduct('edit_fee', $category_type);
                break;
            case 'switch_classified_to_auction':
                return $this->point_to_deduct('switch_classified_to_auction', $category_type);
                break;
            case 'switch_auction_to_classified':
                return $this->point_to_deduct('switch_auction_to_classified', $category_type);
                break;
            case 'withdraw_fee_reserve_not_meet':
                return $this->point_to_deduct('withdraw_fee_reserve_not_meet', $category_type);
                break;
            case 'bump_refresh':
                return $this->point_to_deduct('bump_refresh', $category_type);
                break;
            case 'bump_refresh_7_days':
                return $this->point_to_deduct('bump_refresh_7_days', $category_type);
                break;
            case 'feature_7_days':
                return $this->point_to_deduct('feature_7_days', $category_type);
                break;
            case 'feature_14_days':
                return $this->point_to_deduct('feature_14_days', $category_type);
                break;
            case 'highlight_ads':
                return $this->point_to_deduct('highlight_ads', $category_type);
                break;
            case 'feature_7_days':
                return $this->point_to_deduct('feature_7_days', $category_type);
                break;
            case 'relist_ad':
                return $this->point_to_deduct('relist_ad', $category_type);
                break;
            case 'auction_winner_success_fee':
                return $this->point_to_deduct('auction_winner_success_fee', $category_type);
                break;
            case 'buy_decline_the_bid':
                return $this->point_to_deduct('buy_decline_the_bid', $category_type);
                break;
            case 'check_seller_info':
                return $this->point_to_deduct('check_seller_info', $category_type);
                break;
            case 'check_buyer_info_by_seller':
                return $this->point_to_deduct('check_buyer_info_by_seller', $category_type);
                break;
            case 'success_fee':
                return $this->point_to_deduct('success_fee', $category_type);
                break;
            case 'new_auction_listing_lock_amount':
                return $this->point_to_deduct('new_auction_listing_lock_amount', $category_type);
                break;
            case 'high_bidder_lock_amount':
                return $this->point_to_deduct('high_bidder_lock_amount', $category_type);
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * Helper function for deduct_amount
     */
    private function point_to_deduct($action, $category_type)
    {
        return DB::table('user_point_setting')->where('point_category', $category_type)->where('transaction_type', $action)->value('deduction_point');
    }

    public function lock_point_for_user($user_id, $action, $lock_description, $listing_id, $trigger_broadcast = true)
    {
        $point_to_deduct = $this->deduct_amount($user_id, $action);
        $isLockExistForThisListing = UserPointLock::where('user_id', $user_id)
            ->where('listing_id', $listing_id)->where('point_lock_type', $action)->exists();

        if (!$isLockExistForThisListing) {
            $lock_now = UserPointLock::create([
                'user_id' => $user_id,
                'point_lock_type' => $action,
                'lock_point_amount' => $point_to_deduct,
                'point_lock_description' => $lock_description,
                'listing_id' => $listing_id,
            ]);
            //When success, deduct also from user point
            //Only deduct when there is valid amount
            User::where('id', $user_id)->update([
                'bp_point' => DB::raw('bp_point - ' . $point_to_deduct)
            ]);

            //Lets notify user
            if($trigger_broadcast) {
                broadcast(new BpPointUpdateEvent($user_id));
            }
            return $lock_now->exists();
        }

        return false;
    }

    public function release_lock_point($user_id, $action, $listing_id, $trigger_broadcast = true)
    {
        DB::beginTransaction();
        try {
            $lock_details =DB::table("user_point_lock")->where('user_id', $user_id)
                ->where('listing_id', $listing_id)->where('point_lock_type', $action)->get();

            //add to user bp_point
            if ($lock_details->isNotEmpty()) {

                $lock_amount = $lock_details[0]->lock_point_amount;
                //insert to release log table

                DB::table("user_point_release_log")->insert([
                    'id' => $lock_details[0]->id,
                    'user_id' => $lock_details[0]->user_id,
                    'point_lock_type' => $lock_details[0]->point_lock_type,
                    'lock_point_amount' => $lock_details[0]->lock_point_amount,
                    'point_lock_description' => $lock_details[0]->point_lock_description,
                    'other_details' => $lock_details[0]->other_details,
                    'listing_id' => $lock_details[0]->listing_id,
                    'created_at' => $lock_details[0]->created_at,
                    'updated_at' => $lock_details[0]->updated_at
                ]);

                UserPointLock::where('user_id', $user_id)
                    ->where('listing_id', $listing_id)->where('point_lock_type', $action)->delete();
                User::where('id', $user_id)->update([
                    'bp_point' => DB::raw('bp_point + ' . $lock_amount)
                ]);

                //Lets notify user
                if($trigger_broadcast) {
                    broadcast(new BpPointUpdateEvent($user_id));
                }
            }


            DB::commit();
            return true;

        } catch (Throwable $e) {

            DB::rollback();
            return false;
        }

        return false;
    }

    //This is only testing purpose
    public function test()
    {
        $listing_id = 148;

        // Remove high bidder amount
        $action = 'high_bidder_lock_amount';
        $user_ids_lock_amount = UserPointLock::where('listing_id', $listing_id)->where('point_lock_type',  $action)->pluck('user_id');

        foreach ($user_ids_lock_amount as $user_id) {
            app(PointDeductionController::class)->release_lock_point($user_id, $action, $listing_id, false);
        }

        // //Now lock amount for high bidder
        // $currentbid = ListingAuctionBid::where('listing_id', $listing_id)->max('bid_amount');
        // if ($currentbid !== null) {
        //     $high_bidder_id = ListingAuctionBid::where('listing_id', $listing_id)->where('bid_amount', $currentbid)->value('user_id');
        //     $ad_title = Listing::where('id', $listing_id)->value('ad_title');

        //     $lock_description = 'Point lock since you are top bidder for ' . $ad_title;
        //     app(PointDeductionController::class)->lock_point_for_user($high_bidder_id, $action, $lock_description, $listing_id, false);
        // }
    }
}
