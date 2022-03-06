<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Company;
use App\Models\Listing\Listing;
use Illuminate\Support\Facades\DB;
use App\Models\Listing\ListingBumpIds;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use Carbon\Carbon;
use App\Models\Listing\ListingAuctionBid;
use App\Models\Listing\ListingUserActivity;

class ProfileController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function logout()
    {
        $user = Auth::user();

        if (!is_null($user)) {
            $deleted = $user->currentAccessToken()->delete();

            if ($deleted) {
                return $this->successResponse($deleted, "Logout success");
            } else {
                return $this->errorResponse("Logout fail", 404);
            }
        } else {
            return $this->errorResponse("Whoops! no user found", 404);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse("Current password does not match", 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return $this->successResponse(true, "Password successfully updated!");
    }

    public function updatePersonalUserData(Request $request)
    {
        $user = Auth::user();

        if ($request->password) {
            return $this->errorResponse("Password update not allowed", 422);
        }
        $validator = Validator::make($request->all(), [
            'email' => 'email|unique:users,email, ' . auth()->user()->id . ',id',
            'mobile_no' => 'min:10|unique:users,mobile_no,' . auth()->user()->id,
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422,);
        }
        $requestedData = request()->except(['password', 'id', 'status', 'user_type']);

        //If user update mobile no, unverify only if mobile number is different
        if ($request->has('mobile_no')) {
            $isSameMobileNumber = User::where('mobile_no', $request->mobile_no)->where('id', $user->id)->first();
            if ($isSameMobileNumber === null) {
                $requestedData['mobile_verification_code'] = null;
                $requestedData['is_mobile_verified'] = false;
            }
        }

        $data = User::find($user->id);
        $data->fill($requestedData);
        $data->save();

        return $this->successResponse($data, "Personal details updated!");
    }

    public function getUserData()
    {
        $user = Auth::user();

        $data = User::where('id', $user->id)->get();
        $companyData = Company::where('id', $user->company_id)->get();
        $data[0]['company'] = $companyData;

        if (!is_null($user)) {
            return $this->successResponse($data);
        } else {
            return $this->errorResponse("User not found", 404);
        }
    }

    public function getUserCompanyDetails()
    {
        $user = Auth::user();
        $data = Company::where('id', $user->company_id)->get();

        if ($data->count()) {
            return $this->successResponse($data);
        } else {
            return $this->errorResponse("Company not exist", 404);
        }
    }

    /**
     * TODO: Please do more validation for each input type during update
     */
    public function updateUserCompanyDetails(Request $request)
    {
        if (empty($request->all())) {
            return $this->errorResponse("No data provided to update for company", 422);
        }

        $result = array_filter($request->all());
        $userComapnyId = User::where('id', Auth::user()->id)->value('company_id');


        if (array_key_exists('new_company_registration_no', $result)) {
            $isExistNewCompanyRegistration = Company::where('id', '!=', $userComapnyId)->where('new_company_registration_no', $result['new_company_registration_no'])->exists();
            if ($isExistNewCompanyRegistration) {
                return $this->errorResponse('This new regiration number is already in use, please choose a different one', 422,);
            }
        }

        if (array_key_exists('old_company_registration_no', $result)) {
            $result['old_cpompany_registration_no'] = $result['old_company_registration_no'];
            $isExistOldCompanyRegistration = Company::where('id', '!=', $userComapnyId)->where('old_cpompany_registration_no', $result['old_company_registration_no'])->exists();
            if ($isExistOldCompanyRegistration) {
                return $this->errorResponse('This old regiration number is already in use, please choose a different one', 422,);
            }
        }

        if (array_key_exists('old_company_registration_no', $result) && array_key_exists('new_company_registration_no', $result)) {
            if ($result['new_company_registration_no'] == $result['old_company_registration_no']) {
                return $this->errorResponse('Old and new company registration number can not be same', 422,);
            }
        }

        $isComapnyEmailExist = Company::where('id', '!=', $userComapnyId)->where('company_email', $result['company_email'])->exists();
        if ($isComapnyEmailExist) {
            return $this->errorResponse('This Email is already in use, please choose a different one', 422,);
        }

        $isComapnyPhoneExist = Company::where('id', '!=', $userComapnyId)->where('company_phone_number', $result['company_phone_number'])->exists();
        if ($isComapnyPhoneExist) {
            return $this->errorResponse('This Phone number is already in use, please choose a different one', 422,);
        }

        $user = Auth::user();
        $companyId = $user->company_id;
        $data = Company::find($companyId);
        if ($data !== NULL) {
            $data->fill($result);
            $data->save();
            return $this->successResponse($data, "Company details updated!");
        }

        return $this->errorResponse("Whoops! Company does not exist", 404);
    }

    public function getUserAdsList($status, $limit = 9, Listing $listing)
    {
        $limit = isset($limit) ? $limit : 9;

        $user = Auth::user();
        $listing = $listing->newQuery()->where('user_id', $user->id);
        $listing->with('listing_auction_bid:listing_id,bid_amount as current_bid_amount');
        $listing->with('listing_bump:listing_id,bump_end_date');

        if ($status === 'pending_approval') {
            $listing->where(function ($query) {
                $query->where('listing_status', 'pending_approval')
                    ->orWhere('listing_status', 'rejected');
            });
        }

        if ($status === 'draft') {
            $listing->where('listing_status', 'draft');
        }

        if ($status === 'published') {
            $listing->where('listing_status', 'published');
        }

        if ($status === 'sold') {
            $listing->where('listing_status', 'sold');
        }

        if ($status === 'unsold') {
            $listing->where('listing_status', 'unsold');
        }

        if ($status === 'deleted') {
            $listing->where('listing_status', 'deleted');
        }

        if ($status === 'expired') {
            $listing->where(function ($query) {
                $query->where('listing_status', 'expired');
                $query->orWhere('listing_status', 'unsold');
            });
        }
        $listing->orderBy('id', 'desc');

        $listing = $listing->paginate($limit);

        $listing->map(function ($list) {
            if ($list->listing_status === 'sold') {
                $userid = ListingUserActivity::where('listing_id', $list->id)
                    ->where(function ($query) {
                        $query->where('is_bought', 1);
                        $query->orWhere('is_won', 1);
                    })
                    ->value('user_id');
                $list['buyer_info'] = User::select('first_name', 'last_name', 'email', 'mobile_no', 'avatar', 'address', 'city', 'state', 'country', 'postal_code')
                    ->where('id', $userid)->first();
            }
            $list['page_position'] = $this->getListingPosition($list->id);
            //to get bid details
            $bid_details = DB::table('listing_auction_bid')->where('listing_id', $list->id)
                ->selectRaw("COUNT(bid_amount) AS total_bid_received, COUNT(DISTINCT user_id) AS total_bidder_join")->first();
            $list['bid_details'] = $bid_details;
        });

        return $this->successResponse($listing);
    }

    // send 1=is_won, 2=is_lost, 3=is_favorite
    /**
     * TODO: Improve later when we done with auction
     * Since we also need to get auction activity for this user for this listing
     * and also improve query and minimize code
     */
    public function getUserBuyingList($status, $limit = 9)
    {
        $limit = isset($limit) ? $limit : 9;

        $user = Auth::user();
        $listing = NULL;
        if ($status == 1) {
            $listing = Listing::join('listing_user_activity', 'listing_user_activity.listing_id', '=', 'listing.id')
                ->where('listing_user_activity.user_id', $user->id)
                ->where(function ($query) {
                    $query->where('listing_user_activity.is_won', 1);
                    $query->orWhere('listing_user_activity.is_bought', 1);
                })
                ->orderBy('listing.id', 'desc')
                ->paginate($limit);
        }

        if ($status == 2) {
            $listing = Listing::join('listing_user_activity', 'listing_user_activity.listing_id', '=', 'listing.id')
                ->where('listing_user_activity.user_id', $user->id)
                ->where('listing_user_activity.is_lost', 1)->orderBy('listing.id', 'desc')->paginate($limit);
            if($listing !== null) {
                $listing->map(function ($list) {
                    $is_bought = DB::table('listing_user_activity')->where('listing_id', $list->id)->where('is_bought', 1)->exists();
                    $is_won_over_auction = DB::table('listing_user_activity')->where('listing_id', $list->id)->where('is_won', 1)->exists();
                    $is_won_over_auction = DB::table('listing_user_activity')->where('listing_id', $list->id)->where('is_won', 1)->exists();
                    $list['is_bought_over_buy_now'] = $is_bought;
                    $list['is_won_over_auction'] = $is_won_over_auction;
                 });
            }
        }

        if ($status == 3) {
            $listing = Listing::join('listing_user_activity', 'listing_user_activity.listing_id', '=', 'listing.id')
                ->where('listing_user_activity.user_id', $user->id)
                ->where('listing_user_activity.is_favorite', 1)->orderBy('listing.id', 'desc')->paginate($limit);
        }

        if ($status == 4) {
            $currentBidParticipation = ListingAuctionBid::where('listing_auction_bid.user_id', $user->id)
                ->join('listing', 'listing.id', 'listing_auction_bid.listing_id')
                ->where('listing.listing_status', 'published')->distinct()->pluck('listing.id');

            $listing = Listing::whereIn('id', $currentBidParticipation)->orderBy('id', 'desc')->paginate($limit);

            if ($listing !== null) {
                $listing->map(function ($list) {

                    $listing_auction_bid = DB::table('listing_auction_bid')->where('listing_id', $list->id)
                        ->selectRaw("MAX(bid_amount) AS current_bid_amount")->first();
                    $list['listing_auction_bid'] = $listing_auction_bid->current_bid_amount === null ? null : $listing_auction_bid;

                    $bid_details = DB::table('listing_auction_bid')->where('listing_id', $list->id)
                        ->selectRaw("COUNT(bid_amount) AS total_bid_received, COUNT(DISTINCT user_id) AS total_bidder_join")->first();
                    $list['bid_details'] = $bid_details;
                });
            }

            return $this->successResponse($listing);
        }

        return $this->successResponse($listing);
    }

    /**
     * Helper function to get where the post could be locatied
     */
    protected function getListingPosition($listing_id, $paginate = 25)
    {
        $this_listing = Listing::where('id',$listing_id)->first();
        $total_listing_until_this_listing_id = 0;

        $listing = Listing::where(function($query) use ($this_listing){
            //(updated_at is same, sort by id) or updated_at more than
             return $query->where('id', '>', $this_listing->id)
             ->where('updated_at', $this_listing->updated_at)
             ->orWhere('updated_at', '>', $this_listing->updated_at);
         });

        if(!$this_listing->is_feature){
            $listing->orWhere('is_feature',1);
        }else{
            $listing->where('is_feature',1);
        }

        $total_listing_until_this_listing_id = $listing->where('listing_status','published')->where('categories_id',$this_listing->categories_id)->count();

        $ranking = $total_listing_until_this_listing_id + 1;
        $page = intdiv($ranking, $paginate) + 1;
        $temp_pos = $ranking % $paginate;
        $position =  $temp_pos == 0? $paginate: $temp_pos;

        $result = array();
        $result['total_page'] = $page;
        $result['position_in_which_page'] = $position;
        if ($page > 2) {
            $result['is_high_visibility'] = false;
            $result['is_low_visibility'] = true;
        } else {
            $result['is_high_visibility'] = true;
            $result['is_low_visibility'] = false;
        }

        return (object) $result;
    }

    public function statusUpdateUserListing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'listing_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        if ($request->input('status') !== 'sold' && $request->input('status') !== 'deleted') {
            return $this->errorResponse('Status must either sold or deleted', 422);
        }
        //If this listinng is sold already, user not allowed to delete it
        $isSold = Listing::where('id', $request->input('listing_id'))->where('listing_status', 'sold')->exists();
        if ($isSold && $request->input('status') === 'deleted') {
            return $this->errorResponse('You are not allowd to delete sold list', 422);
        }
        //Check if it user own the listing
        $isOwnerOftheListing = Listing::where('id', $request->listing_id)->where('user_id', Auth::user()->id)->exists();
        if (!$isOwnerOftheListing) {
            return $this->errorResponse('Please stop! You should not try to delete other listing which you did not own', 422);
        }

        //Check if update other user if they participate in this listing, mark as lost
        $bidder_id = ListingAuctionBid::where('listing_id', $request->input('listing_id'))->distinct()->pluck('user_id');
        foreach ($bidder_id as $userid) {
            $matchThese = ['listing_id' => $request->input('listing_id'), 'user_id' => $userid];
            ListingUserActivity::updateOrCreate($matchThese, [
                "listing_id" => $request->input('listing_id'),
                "user_id" => $userid,
                "is_lost" => 1
            ]);
        }
        //If all validation pass lets update
        $user_id = Auth::user()->id;
        $isUpdate = Listing::where('id', $request->input('listing_id'))->where('user_id', $user_id)->update([
            'listing_status' => $request->input('status'),
            'end_date' => now()
        ]);

        if ($isUpdate) {
            return $this->successResponse('Your update request was successful');
        }

        //No update means listing does not exist or user not own the list
        return $this->errorResponse("You are not owner of this listing");
    }

    /**
     * promote_type must be = bump, highlight or feature
     * Improve this in future to make it shorter
     * please send also `no_of_days` for bump and feature add
     * TODO: Update logic, not promote add except published listing, and also check if ad belongs to current user
     */
    public function promoteAds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        $user = Auth::user();
        $currentDate = Carbon::now()->toDateTimeString();

        if ($request->has('bump') && $request->input('bump') !== null) {
            $no_of_days = $request->bump_no_of_days ? $request->bump_no_of_days : 0;
            $bump_end_date = now()->addDays($no_of_days);

            if ($no_of_days == 7) {
                $isAlreadyBumped = ListingBumpIds::where('listing_id', $request->listing_id)
                    ->where('is_bump', true)->where('bump_end_date', '>', $currentDate)->exists();
                if ($isAlreadyBumped) {
                    return $this->errorResponse('New bump request only can perform once existing one expired', 422,);
                }
            }

            /**
             * Point deduction
             */
            $action_name = 'bump_refresh';
            if ($no_of_days == 7) {
                $action_name = 'bump_refresh_7_days';
            }
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action_name)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
            }

            //Update only if it is 7 days
            if ($no_of_days == 7) {
                $matchThese = ['listing_id' => $request->listing_id];
                ListingBumpIds::updateOrCreate($matchThese, [
                    "listing_id" => $request->listing_id,
                    "is_bump" => true,
                    "bump_end_date" => $bump_end_date,
                    "duration_of_bump" => $no_of_days
                ]);
            }

            app(PointDeductionController::class)->deduct_point_from_user($user->id, $action_name, $request->listing_id, 'Your bump request was successful');
            //Instant bump
            Listing::where('id', $request->listing_id)->update(['updated_at' => now()]);
        }

        if ($request->has('highlight') && $request->input('highlight') !== null) {

            $listing = Listing::where('id', $request->listing_id)->first();
            $highlight_end_date = $listing->listing_type == 'auction' ? $listing->end_date : now()->addDays(60);

            /**
             * Any other validation
             */
            $isHighligheted = Listing::where('id', $request->listing_id)
                ->where('is_highlight', true)->exists();
            if ($isHighligheted) {
                return $this->errorResponse('Your ads already highlighted', 422,);
            }

            $action_name = 'highlight_ads';
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action_name)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422);
            }

            Listing::where('id', $request->listing_id)->update([
                'is_highlight' => true,
                'highlight_end_date' => $highlight_end_date,
            ]);


            app(PointDeductionController::class)->deduct_point_from_user($user->id, $action_name, $request->listing_id);
        }

        /**
         * TODO: Check if it is already feature and date not end
         */
        if ($request->has('feature') && $request->input('feature') !== null) {
            $no_of_days = $request->input('feature_no_of_days') ? $request->input('feature_no_of_days') : 0;
            $highlight_end_date = now()->addDays($no_of_days);

            /**
             * Any other validation
             */
            $isHighligheted = Listing::where('id', $request->listing_id)
                ->where('is_feature', true)->where('feature_end_date', '>', $currentDate)->exists();
            if ($isHighligheted) {
                return $this->errorResponse('New feature request only can perform once existing one expired', 422,);
            }

            $action_name = 'feature_7_days';
            if ($no_of_days == 14) {
                $action_name = 'feature_14_days';
            }
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action_name)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
            }

            Listing::where('id', $request->listing_id)->update([
                'is_feature' => true,
                'feature_end_date' => $highlight_end_date,
            ]);

            app(PointDeductionController::class)->deduct_point_from_user($user->id, $action_name, $request->listing_id);
        }

        return $this->successResponse("Your request was successful");
    }

    public function isUserFullyVerified(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_verified_for' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        if ($request->is_verified_for != 'bidding' && $request->is_verified_for != 'listing') {
            return $this->errorResponse('Must send bidding or listing', 422);
        }

        $user = Auth::user();
        $user_type = User::where('id', $user->id)->value('user_type');

        if ($user_type == 'user') {
            return $this->private_user_validation($user->id, $request->is_verified_for);
        }

        if ($user_type == 'agent' || $user_type == 'dealer') {
            return $this->dealer_and_agent_user_validation($user->id, $request->is_verified_for);
        }

        return $this->errorResponse('Invalid user type', 422);
    }

    protected function private_user_validation($user_id, $validation_for)
    {
        $isMobileVerified = User::where('id', $user_id)->where('is_mobile_verified', 1)->exists();
        $isUserIcUploaded = User::where('id', $user_id)->whereNotNull('user_ic_photo')->where('user_ic_photo', '<>', '')->exists();
        /**
         * 1. User needs to verify mobile number
         * 2. Need to input and upload IC in profile
         * 3. Need to have deposit (points) for each bid ( Minimum 200 )
         */
        if ($validation_for === 'bidding') {
            $action = 'high_bidder_lock_amount';
            $point_required = app(PointDeductionController::class)->deduct_amount($user_id, $action);
            $isUserHaveEnoughPoint = User::where('id', $user_id)->where('bp_point', '>=', $point_required)->exists();

            if (!$isMobileVerified) {
                return $this->errorResponseWithKey('Please verify mobile number to bid', 'unverified-phone', 422);
            }

            if (!$isUserIcUploaded) {
                return $this->errorResponseWithKey('Please upload IC in profile to bid', 'ic-not-uploaded', 422);
            }

            if (!$isUserHaveEnoughPoint) {
                return $this->errorResponseWithKey('Please top up to bid, make sure you have minimum ' . $point_required . ' point', 'not-enough-point', 422);
            }

            return $this->successResponse(true, 'You are allowd to bid');
        }
        /**
         * 1. User needs to verify mobile number
         * 2. Need to input and upload IC in profile
         * 3. Need to have deposit (points) for each listing created ( Minimum 200 )
         * 4. Submitted listing will be pending. Needs to go thru standard listing approval by admin
         *  - Keeping this thing seperate, although code with bidding looks same, so that if there is additional
         *      requiremnt, we can add them seperately
         */

        if ($validation_for === 'listing') {
            $action = 'new_auction_listing_lock_amount';
            $point_required = app(PointDeductionController::class)->deduct_amount($user_id, $action);
            $isUserHaveEnoughPoint = User::where('id', $user_id)->where('bp_point', '>=', $point_required)->exists();

            if (!$isMobileVerified) {
                return $this->errorResponseWithKey('Please verify mobile number for listing', 'unverified-phone', 422);
            }

            if (!$isUserIcUploaded) {
                return $this->errorResponseWithKey('Please upload IC in profile for listing', 'ic-not-uploaded', 422);
            }

            if (!$isUserHaveEnoughPoint) {
                return $this->errorResponseWithKey('Please top up to create listing, make sure you have minimum 200 point', 'not-enough-point', 422);
            }

            return $this->successResponse(true, 'You are allowd to bid');
        }

        return $this->errorResponse('Invalid validation request, please send bidding or listing only', 422);
    }

    protected function dealer_and_agent_user_validation($user_id, $validation_for)
    {
        $isMobileVerified = User::where('id', $user_id)->where('is_mobile_verified', 1)->exists();

        $companyId = User::where('id', $user_id)->value('company_id');
        $isaCompanyVerified = Company::where('id', $companyId)
            ->whereNotNull('company_name')->whereNotNull('company_email')
            ->whereNotNull('company_phone_number')->whereNotNull('address')
            ->whereNotNull('city')->whereNotNull('state')
            ->whereNotNull('zip_code')->whereNotNull('country')
            ->whereNotNull('company_cert_ssm_file')->where(function ($query) {
                $query->whereNotNull('new_company_registration_no');
                $query->orWhereNotNull('old_cpompany_registration_no');
            })->exists();

        $isAccountVerifyByAdmin = User::where('id', $user_id)->where('status_admin', 'approve')->exists();

        if ($validation_for === 'bidding') {

            $action = 'high_bidder_lock_amount';
            $point_required = app(PointDeductionController::class)->deduct_amount($user_id, $action);
            $isUserHaveEnoughPointForBidding = User::where('id', $user_id)->where('bp_point', '>=', $point_required)->exists();

            if (!$isMobileVerified) {
                return $this->errorResponseWithKey('Please verify mobile number to bid', 'unverified-phone', 422);
            }

            if (!$isaCompanyVerified) {
                return $this->errorResponseWithKey('Please complete company info to start bidding', 'unverified-company', 422);
            }

            if (!$isAccountVerifyByAdmin) {
                return $this->errorResponseWithKey('Your account pending verification by byparts admin', 'pending-by-admin', 422);
            }

            if (!$isUserHaveEnoughPointForBidding) {
                return $this->errorResponseWithKey('Please top up to bid, make sure you have minimum ' . $point_required . ' point', 'not-enough-point', 422);
            }

            return $this->successResponse(true, 'You are allowd to bid');
        }

        /**
         * - Keeping this thing seperate, although code with bidding looks same, so that if there is additional
         *     requiremnt, we can add them seperately
         */
        if ($validation_for === 'listing') {
            $action = 'new_auction_listing_lock_amount';
            $point_required = app(PointDeductionController::class)->deduct_amount($user_id, $action);
            $isUserHaveEnoughPointForListing = User::where('id', $user_id)->where('bp_point', '>=', $point_required)->exists();

            if (!$isMobileVerified) {
                return $this->errorResponseWithKey('Please verify mobile number to create listing', 'unverified-phone', 422);
            }

            if (!$isaCompanyVerified) {
                return $this->errorResponseWithKey('Please complete company info to create listing', 'unverified-company', 422);
            }

            if (!$isAccountVerifyByAdmin) {
                return $this->errorResponseWithKey('Your account pending verification by byparts admin', 'pending-by-admin', 422);
            }

            if (!$isUserHaveEnoughPointForListing) {
                return $this->errorResponseWithKey('Please top up to create listing, make sure you have minimum ' . $point_required . ' point', 'not-enough-point', 422);
            }

            return $this->successResponse(true, 'You are allowd to bid');
        }

        return $this->errorResponse('Invalid validation request, please send bidding or listing only', 422);
    }
}
