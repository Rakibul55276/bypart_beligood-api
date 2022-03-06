<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing\Listing;
use App\Models\Company;
use App\Models\Listing\ListingUserActivity;
use App\Models\ListingCategory;
use App\Models\ListingSubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmailToSellerForClassified;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use App\Models\Payment\UserPointDeductionHistory;
use Carbon\Carbon;
use Throwable;
use App\Models\Listing\ListingQuestionAnswer;
use App\Helper\ListingRelated;

class CarListingController extends ApiController
{

    public function getCategories(Request $request)
    {
        $res = array();
        $category = ListingCategory::select("id","name")->where('listing_categories.is_active',1)->get();
        foreach($category as $value){
            $value->sub = ListingSubCategory::select("id","name")->where('listing_sub_categories.is_active',1)->get();
            array_push($res, $value);
        }
        return $this->successResponse($res);
    }

    public function searchListing(Request $request, Listing $listing)
    {
        $listing = $listing->newQuery();

        if (!$request->has('categories_id')) {
            return $this->errorResponse("No category id",422);
        }

        $listing->where('categories_id', $request->input('categories_id'));
        //if set limit from fe
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        // Search base on car_condition.
        if ($request->has('car_condition')) {
            $listing->where('car_condition', $request->input('car_condition'));
        }

        // Search base on make.
        if ($request->has('car_make_name')) {
            $listing->where('car_make_name', $request->input('car_make_name'));
        }

        // Search base on car_body_type.
        if ($request->has('car_body_type')) {
            $listing->where('car_body_type', $request->input('car_body_type'));
        }

        // Search base on state.
        if ($request->has('state')) {
            $listing->where('state', $request->input('state'));
        }

        // Search base on model.
        if ($request->has('model')) {
            $listing->where('model', $request->input('model'));
        }

        // Search base on fuel_type.
        if ($request->has('fuel_type')) {
            $listing->where('fuel_type', $request->input('fuel_type'));
        }

        // Search base on transmission
        if ($request->has('transmission')) {
            $listing->where('transmission', $request->input('transmission'));
        }

        // Search base on listing_type
        if ($request->has('listing_type')) {
            $listing->where('listing_type', $request->input('listing_type'));
        }

        /**
         * Search base on min price ( greater than )
         * Search base on max price ( less than )
         */
        if ($request->has('asking_price_min')) {
            $listing->where(function ($query) use ($request) {
                $query->where('asking_price', '>=', $request->input('asking_price_min'))
                    ->orWhere('starting_price', '>=', $request->input('asking_price_min'));
            });
        }
        if ($request->has('asking_price_max')) {
            $listing->where(function ($query) use ($request) {
                $query->where('asking_price', '<=', $request->input('asking_price_max'))
                    ->orWhere('starting_price', '<=', $request->input('asking_price_max'));
            });
        }

        /**
         * Search base on min engine_size ( greater than )
         * Search base on max engine_size ( less than )
         */
        if ($request->has('engine_size_min')) {
            $listing->where('engine_size', '>=', $request->input('engine_size_min'));
        }
        if ($request->has('engine_size_max')) {
            $listing->where('engine_size', '<=', $request->input('engine_size_max'));
        }

        /**
         * Search base on min manufacture_year ( greater than )
         * Search base on max manufacture_year ( less than )
         */
        if ($request->has('manufacture_year_min')) {
            $listing->where('manufacture_year', '>=', $request->input('manufacture_year_min'));
        }
        if ($request->has('manufacture_year_max')) {
            $listing->where('manufacture_year', '<=', $request->input('manufacture_year_max'));
        }

        /**
         * Search base on min doors ( greater than )
         * Search base on max doors ( less than )
         */
        if ($request->has('doors_min')) {
            $listing->where('doors', '>=', $request->input('doors_min'));
        }
        if ($request->has('doors_max')) {
            $listing->where('doors', '<=', $request->input('doors_max'));
        }

        if ($request->has('doors_min')) {
            $listing->where('doors', '>=', $request->input('doors_min'));
        }
        if ($request->has('doors_max')) {
            $listing->where('doors', '<=', $request->input('doors_max'));
        }

        /**
         * Search base on min odometer (mileage ) ( greater than )
         * Search base on max odometer (mileage ) ( less than )
         */
        if ($request->has('mileage_min')) {
            $listing->where('mileage', '>=', $request->input('mileage_min'));
        }
        if ($request->has('mileage_max')) {
            $listing->where('mileage', '<=', $request->input('mileage_max'));
        }

        // Other condition
        $listing->where('listing_status', 'published')->orderBy('id', 'desc');
        return $this->successResponse($listing->paginate($limit));
    }

    /**
     * TODO: Improve is null and empty
     */
    public function filterListing(Request $request, Listing $listing)
    {
        //Some basic set
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $listing = $listing->newQuery();
        // Only display if published
        $listing->where('listing_status', 'published');

        $listing->where('categories_id', $request->categories_id);

        //search base on string inputed by user
        if ($request->has('search_text') && !is_null($request->input('search_text')) && !empty($request->input('search_text'))) {
            if ($request->input('categories_id') == 1){
                //if this is car listing
                $listing->where(function ($query) use ($request) {
                        $query->where('car_make_name', 'like', '%' . $request->input('search_text') . '%');
                        $query->orWhere('car_body_type', 'like', '%' . $request->input('search_text') . '%');
                        $query->orWhere('model', 'like', '%' . $request->input('search_text') . '%');
                        $query->orWhere('fuel_type', 'like', '%' . $request->input('search_text') . '%');
                        $query->orWhere('ad_title', 'like', '%' . $request->input('search_text') . '%');
                    });
                }
            else if($request->input('categories_id') == 2){
                //else if this is car plate listing
                $listing->where(function ($query) use ($request) {
                    $query->where('car_plate_number', 'like', '%' . $request->input('search_text') . '%');
                    $query->orWhere('ad_title', 'like', '%' . $request->input('search_text') . '%');
                });
            }
        }

        //search car plate number digit
        if ($request->has('car_plate_digit') && !is_null($request->input('car_plate_digit')) && !empty($request->input('car_plate_digit'))) {
            $listing->whereRaw("LENGTH(REGEXP_REPLACE(car_plate_number, '[^0-9]+', '')) In (".implode(',',$request->input('car_plate_digit')).")");
        }

        // Search base on listing_type array.
        if ($request->has('listing_type') && !is_null($request->input('listing_type')) && !empty($request->input('listing_type'))) {
            $listing->whereIn('listing_type', $request->input('listing_type'));
        }

        // Search base on car_condition array.
        if ($request->has('car_condition') && !is_null($request->input('car_condition')) && !empty($request->input('car_condition'))) {
            $listing->whereIn('car_condition', $request->input('car_condition'));
        }

        // Search base on car_make_name array.
        if ($request->has('car_make_name') && !is_null($request->input('car_make_name')) && !empty($request->input('car_make_name'))) {
            $listing->whereIn('car_make_name', $request->input('car_make_name'));
        }

        // Search base on state array.
        if ($request->has('state') && !is_null($request->input('state')) && !empty($request->input('state'))) {
            $listing->whereIn('state', $request->input('state'));
        }

        // Search base on transmission array.
        if ($request->has('transmission') && !is_null($request->input('transmission')) && !empty($request->input('transmission'))) {
            $listing->whereIn('transmission', $request->input('transmission'));
        }

        // Search base on car_body_type array.
        if ($request->has('car_body_type') && !is_null($request->input('car_body_type')) && !empty($request->input('car_body_type'))) {
            $listing->whereIn('car_body_type', $request->input('car_body_type'));
        }

        // Search base on color array.
        if ($request->has('color') && !is_null($request->input('color')) && !empty($request->input('color'))) {
            $listing->whereIn('color', $request->input('color'));
        }

        // Search base on fuel_type array.
        if ($request->has('fuel_type') && !is_null($request->input('fuel_type')) && !empty($request->input('fuel_type'))) {
            $listing->whereIn('fuel_type', $request->input('fuel_type'));
        }

        /**
         * Search base on min price ( greater than )
         * Search base on max price ( less than )
         */
        if ($request->has('asking_price_min') && !is_null($request->input('asking_price_min')) && !empty($request->input('asking_price_min'))) {
            $listing->where(function ($query) use ($request) {
                $query->where('asking_price', '>=', $request->input('asking_price_min'))
                    ->orWhere('starting_price', '>=', $request->input('asking_price_min'));
            });
        }
        if ($request->has('asking_price_max') && !is_null($request->input('asking_price_max')) && !empty($request->input('asking_price_max'))) {
            $listing->where(function ($query) use ($request) {
                $query->where('asking_price', '<=', $request->input('asking_price_max'))
                    ->orWhere('starting_price', '<=', $request->input('asking_price_max'));
            });
        }

        /**
         * Search base on min engine_size ( greater than )
         * Search base on max engine_size ( less than )
         */
        if ($request->has('engine_size_min') && !is_null($request->input('engine_size_min')) && !empty($request->input('engine_size_min'))) {
            $listing->where('engine_size', '>=', $request->input('engine_size_min'));
        }
        if ($request->has('engine_size_max') && !is_null($request->input('engine_size_max')) && !empty($request->input('engine_size_max'))) {
            $listing->where('engine_size', '<=', $request->input('engine_size_max'));
        }

        /**
         * Search base on min manufacture_year ( greater than )
         * Search base on max manufacture_year ( less than )
         */
        if ($request->has('manufacture_year_min') && !is_null($request->input('manufacture_year_min')) && !empty($request->input('manufacture_year_min'))) {
            $listing->where('manufacture_year', '>=', $request->input('manufacture_year_min'));
        }
        if ($request->has('manufacture_year_max') && !is_null($request->input('manufacture_year_max')) && !empty($request->input('manufacture_year_max'))) {
            $listing->where('manufacture_year', '<=', $request->input('manufacture_year_max'));
        }

        /**
         * Search base on min doors ( greater than )
         * Search base on max doors ( less than )
         */
        if ($request->has('doors_min') && !is_null($request->input('doors_min')) && !empty($request->input('doors_min'))) {
            $listing->where('doors', '>=', $request->input('doors_min'));
        }
        if ($request->has('doors_max') && !is_null($request->input('doors_max')) && !empty($request->input('doors_max'))) {
            $listing->where('doors', '<=', $request->input('doors_max'));
        }

        if ($request->has('doors_min') && !is_null($request->input('doors_min')) && !empty($request->input('doors_min'))) {
            $listing->where('doors', '>=', $request->input('doors_min'));
        }
        if ($request->has('doors_max') && !is_null($request->input('doors_max')) && !empty($request->input('doors_max'))) {
            $listing->where('doors', '<=', $request->input('doors_max'));
        }

        /**
         * Search base on min odometer (mileage ) ( greater than )
         * Search base on max odometer (mileage ) ( less than )
         */
        if ($request->has('mileage_min') && !is_null($request->input('mileage_min')) && !empty($request->input('mileage_min'))) {
            $listing->where('mileage', '>=', $request->input('mileage_min'));
        }
        if ($request->has('mileage_max') && !is_null($request->input('mileage_max')) && !empty($request->input('mileage_max'))) {
            $listing->where('mileage', '<=', $request->input('mileage_max'));
        }

        // Search base on specific user id to find there all published lists.
        if ($request->has('user_id') && !is_null($request->input('user_id'))) {
            $listing->where('user_id', $request->input('user_id'));
        }
        /**
         * Getting current bid price
         */
        $listing->with('listing_auction_bid:listing_id,bid_amount as current_bid_amount');

        /**
         * Sorting, orderby, Default sort if only sort not provided by param
         * start_date = auction start date
         * end_fate = auction end date
         * - means ignore null
         */
        if (!is_null($request->input('sort'))) {
            switch ($request->input('sort')) {
                case 'price_low_to_high':
                    /**
                     * Firstly we get max bid amount by subquery
                     * - Then use leftJoinSub to query and name it as current_bid_amount
                     * - Now, we can use this inside CASE
                     */
                    $subquery = DB::table('listing_auction_bid')
                        ->select(DB::raw("listing_id,MAX(bid_amount) as current_bid_amount"))
                        ->groupBy('listing_id');

                    $listing
                        ->leftJoinSub($subquery, 'current_bid_amount', function ($join) {
                            $join->on('listing.id', '=', 'current_bid_amount.listing_id');
                        })
                        ->select('*')
                        ->addSelect(DB::raw('CASE WHEN current_bid_amount IS NOT null THEN current_bid_amount WHEN asking_price IS NOT null THEN asking_price ELSE starting_price END AS sorting_price'))
                        ->orderBy('is_feature', 'DESC')
                        ->orderBy('sorting_price', 'ASC');
                    break;
                case 'price_high_to_low':
                    $subquery = DB::table('listing_auction_bid')
                        ->select(DB::raw("listing_id,MAX(bid_amount) as current_bid_amount"))
                        ->groupBy('listing_id');

                    $listing
                        ->leftJoinSub($subquery, 'current_bid_amount', function ($join) {
                            $join->on('listing.id', '=', 'current_bid_amount.listing_id');
                        })
                        ->select('*')
                        ->addSelect(DB::raw('CASE WHEN current_bid_amount IS NOT null THEN current_bid_amount WHEN asking_price IS NOT null THEN asking_price ELSE starting_price END AS sorting_price'))
                        ->orderBy('is_feature', 'DESC')
                        ->orderBy('sorting_price', 'DESC');
                    break;
                case 'auction_latest':
                    $listing->where('listing_type', 'auction')
                        ->orderBy('is_feature', 'DESC')
                        ->orderBy('end_date', 'desc');
                    break;
                case 'auction_ending_soon':
                    $listing->where('listing_type', 'auction')
                        ->orderBy('end_date', 'asc');
                    break;
                default:
                    $listing->orderBy('is_feature', 'DESC')
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('id','desc');
                    break;
            }
        } else {
            $listing->orderBy('is_feature', 'DESC')->orderBy('updated_at', 'desc');
        }
        //Lets add other info in this api
        // $listing->join('listing_auction_bid', 'listing_auction_bid.listing_id', '=', 'listing.id');
        return $this->successResponse($listing->paginate($limit));
    }

    private function validateListing(Request $request)
    {

        if (!$request->categories_id) return $this->errorResponse('No categories id', 422,);

        if ($request->categories_id == 1){
            //if it is car listing
            $validator = Validator::make($request->all(), [
                'car_condition' => 'required',
                'car_body_type' => 'required',
                'car_make_name' => 'required',
                'mileage' => 'required',
                'model' => 'required',
                'manufacture_year' => 'required',
                'transmission' => 'required',
                'fuel_type' => 'required',
                'engine_size' => 'required',
                'doors' => 'required',
                'color' => 'required',
                'ad_title' => 'required',
                'listing_type' => 'required',
                'area' => 'required',
                'car_images' => 'required',
                'state' => 'required'
            ]);
            if ($validator->fails()) {
                return false;
            }
            return true;
        }else if ($request->categories_id == 2){
            //if it is car plate listing
            $validator = Validator::make($request->all(), [
                'ad_title' => 'required',
                'listing_type' => 'required',
                'car_plate_number' => 'required',
                'car_images' => 'required',
                'state' => 'required',
            ]);
            if ($validator->fails()) {
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    public function saveDraftListing(Request $request)
    {
        $user = Auth::user();
        $listing = new Listing;
        if($request->listing_id){
            $listing = Listing::where('id', $request->listing_id)->where('user_id', $user->id)->first();
            if ($listing === null) {
                return $this->errorResponse("Sorry, you are not allowed to update other user listing", 422);
            }
            if ($listing->listing_status !== 'draft') {
                return $this->errorResponse("Sorry you are not allowed to draft $listing->listing_status ads", 422);
            }
        }

        $listing->fill($request->all());
        $listing->ad_title = ($listing->ad_title? $listing->ad_title: "Untitled");
        $listing->user_id = $user->id;
        $listing->listing_status = "draft";
        $listing->save();
        return $this->successResponse(true, 'Successfully drafted your listing');
    }

    public function saveListing(Request $request)
    {
        $user = Auth::user();
        if(!$this->validateListing($request)) return $this->errorResponse('',422,);

        /**
         * Check if user have enough point to perform this action
         * Only applicable for auction type
         * - Private user listing we lock 200pts
         * - Agent/dealer listing, no lock. But need min 500pts when list
         *   - In db we check against new_auction_listing_lock_amount but for agent only check
         */
        $action = 'new_auction_listing_lock_amount';
        if ($request->listing_type === 'auction') {
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action)) {
                $point_required = app(PointDeductionController::class)->deduct_amount($user->id, $action);
                return $this->errorResponse('Not enough points to perform this action, You required minimum '.$point_required, 422,);
            }
        }

        $data = $request->all();

        if ($request->has('duration_of_auction')) {
            if ($request->input('duration_of_auction') === "7") {
                $data["start_date"] = now();
                $data["end_date"] = now()->addDays(7);
            }

            if ($request->input('duration_of_auction') === "14") {
                $data["start_date"] = now();
                $data["end_date"] = now()->addDays(14);
            }
        }

        //Making sure starting_price, reserve_price, buy_now_price is not null when it is auction
        if ($request->listing_type === 'auction') {
            $starting_price = $request->starting_price === null ? 0 : $request->starting_price;
            $reserve_price = $request->reserve_price === null ? 0 : $request->reserve_price;
            $buy_now_price = $request->buy_now_price === null ? 0 : $request->buy_now_price;

            $data['starting_price'] = $starting_price;
            $data['reserve_price'] = $reserve_price;
            $data['buy_now_price'] = $buy_now_price;
        }

        $data['listing_status'] = "pending_approval";
        $data['user_id'] = $user->id;

        $data['list_relist_date'] = now();

        $save = Listing::create($data);

        if ($save->exists) {
            /**
             * Lets lock point, if this is successful
             * - Only lock for private user and if auction type
             */
            $user_type = Auth::user()->user_type;
            if ($request->listing_type === 'auction' && $user_type == 'user') {
                $lock_description = 'Point lock for your listing '. $request->ad_title;
                app(PointDeductionController::class)->lock_point_for_user($user->id, $action, $lock_description, $save->id);
            }
            return $this->successResponse(true, "Your ad request has been saved and its now pending_approval");
        }
        return $this->errorResponse("Could not save your ad request, please try again", 422);
    }

    /**
     * For relist send: is_relist: 1
     */
    public function updateListing(Request $request)
    {
        $user = Auth::user();

        if(!$this->validateListing($request)) return $this->errorResponse('',422,);

        //check if logged in user is the right owner to update
        $isValidUser = Listing::where('id', $request->listing_id)->where('user_id', $user->id)->first();
        if ($isValidUser === null) {
            return $this->errorResponse("Sorry, you are not allowed to update other user listing", 422);
        }

        $listing = Listing::find($request->listing_id);
        if ($listing->listing_status !== 'pending_approval' && $listing->listing_status !== 'draft' && $listing->listing_status !== 'published' && $listing->listing_status !== 'expired' && $listing->listing_status !== 'rejected') {
            return $this->errorResponse("Sorry you are not allowed to update $listing->listing_status ads", 422);
        }
        //Getting all data from user input
        $data = $request->all();

        /**
         * Following is only applicable when user relisting this
         */
        if ($request->has('is_relist') && $request->is_relist == 1) {

            if ($request->input('duration_of_auction') === 7 || $request->input('duration_of_auction') === "7") {
                $new_start_date = now();
                $new_end_date = now()->addDays(7);
            }
            if ($request->input('duration_of_auction') === "14" || $request->input('duration_of_auction') === 14) {
                $new_start_date = now();
                $new_end_date = now()->addDays(14);
            }

            //Incase post is classified reset those two date to null
            if ($request->listing_type == 'classified') {
                $new_start_date = NULL;
                $new_end_date = NULL;
            }
            /**
             * Checking if user have enough point to perform this action
             *  - Relist with edit = Charge edit `edit_fee`
             *  - Edit and switch < 60 = Charge switch `switch_classified_to_auction` and `switch_auction_to_classified`
             *  - Edit and switch wahtever > 60 = charge relist `relist_ad`
             *  - Complex logic, make sure you know what you are doing when editing this
             */
            $currentDate = Carbon::now()->subDays(60);
            /**
             * True = expired ( greater than 60 days )
             * False = Not expired ( less than 60 days )
             */
            $isMoreThanSixtDays = Listing::where('id', $request->listing_id)
                ->where('created_at', '<', $currentDate)->exists();
            //When false which mean user switching
            $isSwitching = Listing::where('id', $request->listing_id)
                ->where('listing_type', $request->listing_type)->exists();

            if (!$isMoreThanSixtDays && !$isSwitching) {
                $action = 'edit_fee';
            } else if ($isSwitching && !$isMoreThanSixtDays) {
                if ($request->listing_type == 'auction') {
                    $action = 'switch_classified_to_auction';
                } else if ($request->listing_type == 'classified') {
                    $action = 'switch_auction_to_classified';
                }
            } else {
                $action = 'relist_ad';
            }

            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
            }

            DB::beginTransaction();
            try {
                //Set exactly base on user input
                $listing->fill($data);
                $listing->save();
                //Now updating other field to reset
                Listing::where('id', $request->listing_id)->update([
                    'start_date' => $new_start_date,
                    'end_date' => $new_end_date,
                    'is_highlight' => 0,
                    'is_feature' => 0,
                    'listing_status' => 'pending_approval',
                    'feature_end_date' => null,
                    'updated_at' => now(),
                    'list_relist_date' => now()
                ]);

                DB::table('listing_activity')->where('listing_id', $request->listing_id)->delete();
                DB::table('listing_auction_bid')->where('listing_id', $request->listing_id)->delete();
                DB::table('listing_auction_bid_auto')->where('listing_id', $request->listing_id)->delete();
                ListingQuestionAnswer::where('listing_id', $request->listing_id)->delete();

                $point_to_deduct = app(PointDeductionController::class)->deduct_amount($user->id, $action);
                DB::table('user_point_deduction_history')->insert([
                    'user_id' => $user->id,
                    'transaction_type' => $action,
                    'deducted_point' => $point_to_deduct,
                    'other_details' => 'Related to ad relisting',
                    'listing_id' => $request->listing_id,
                ]);

                DB::commit();
            } catch (Throwable $e) {

                DB::rollback();
                return $this->errorResponse("Something went wrong while relisting, please try again");
            }

            return $this->successResponse(true, 'Successfully relisted your listing');
        }

        /**
         * If listing status is published, we need to check if user have enoguh point or not
         */
        if ($listing->listing_status === 'published') {
            $action = 'edit_fee';
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
            }
            app(PointDeductionController::class)->deduct_point_from_user($user->id, $action);
        }

        /**
         * If listing status is rejected, since user editing set this status to pending_approval
         */
        if ($listing->listing_status === 'rejected' || $listing->listing_status === 'draft') {
            $data['listing_status'] = 'pending_approval';
        }

        if ($request->has('duration_of_auction')) {
            if ($request->input('duration_of_auction') === 7 || $request->input('duration_of_auction') === "7") {
                $data["start_date"] = now();
                $data["end_date"] = now()->addDays(7);
            }

            if ($request->input('duration_of_auction') === "14" || $request->input('duration_of_auction') === 14) {
                $data["start_date"] = now();
                $data["end_date"] = now()->addDays(14);
            }
        }
        $listing->fill($data);
        $listing->save();

        return $this->successResponse($data, "Listing ads details updated!");
    }

    /**
     * Update this API in future to not send user and company info if user not yet paid,
     * We did this due to pressure during launch
     */
    public function getSingleListItem($list_id, $edit = null)
    {
        $get_car_list = Listing::with('user:id,first_name,last_name,email,company_id,mobile_no,avatar,address,city,state,country,postal_code,created_at')->with('listing_auction_bid:listing_id,bid_amount as current_bid_amount')->where('id', $list_id)->get();

        if ($get_car_list->isEmpty()) {
            return $this->errorResponse("Item does not exist", 422);
        }

        //Login when edit
        if ($edit) {
            $user = Auth::user();
            $isValidUser = Listing::where('id', $list_id)->where('user_id', $user->id)->first();
            if ($isValidUser === null) {
                return $this->errorResponse("Sorry, you are not allowed to update other user listing", 422);
            }

            $listing = Listing::find($list_id);
            if ($listing->listing_status !== 'pending_approval' && $listing->listing_status !== 'draft' && $listing->listing_status !== 'published' && $listing->listing_status !== 'expired' && $listing->listing_status !== 'rejected' && $listing->listing_status !== 'unsold') {
                return $this->errorResponse("Sorry you are not allowed to update $listing->listing_status ads", 422);
            }
        }

        // If not edit but other validation fail send error response
        if (!$edit) {
            if ($get_car_list[0]->listing_status == "pending_approval" || $get_car_list[0]->listing_status == "draft") {
                return $this->errorResponse("It is still pending_approval", 422);
            }
        }

        // Lets push company details if exists
        $get_car_list[0]['user_compay'] = NULL;
        if ($get_car_list[0]->user->company_id !== NULL) {
            $get_car_list[0]['user_compay'] = Company::where('id', $get_car_list[0]->user->company_id)
                ->first(['id', 'company_name', 'company_logo', 'company_email', 'company_phone_number', 'address', 'city', 'state', 'zip_code', 'country']);
        }

        /**
         * Adding is_favorite for logged in user or not
         * Since, this route need to use in for both logged in or logged out user,
         * We need to use Auth::guard('sanctume') to check if user logged in ot not
         */
        $user = Auth::guard("sanctum")->user();
        $is_favorite = 0;
        if ($user !== NULL) {
            $is_favorite = ListingUserActivity::where('listing_id', $list_id)->where('user_id', $user->id)
                ->where('is_favorite', '1')->exists();
        }
        $get_car_list[0]['is_favorite'] = $is_favorite;

        /**
         * If user logged in and he already paid to see seller contact info,
         * lets sent green signal so that fe can display the information
         */
        $show_contact = false;
        if ($user !== NULL) {
            $show_contact = UserPointDeductionHistory::where('user_id', $user->id)->where('listing_id', $list_id)
                ->where('transaction_type', 'check_seller_info')->exists();
        }
        $get_car_list[0]['show_contact'] = $show_contact;

        return $this->successResponse($get_car_list);
    }

    /**
     * Relist method when list not yet expired
     * - Expired should be check base on 60 dasy from created date
     */
    public function relistWithoutEdit(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        //Check if this listing belongs to this user, else send error response
        $isNotBelongsToCurrentUser = Listing::where('id', $request->listing_id)
            ->where('user_id', $user->id)->get();
        if (!$isNotBelongsToCurrentUser) {
            return $this->errorResponse('Invalid listing relist request', 422,);
        }

        //Check if listing status not expired  and send error, since relist only allow for expired listing
        $isStatusNotExpired = Listing::where('id', $request->listing_id)->where('listing_status', 'expired')->orWhere('listing_status', 'unsold')->exists();
        if(!$isStatusNotExpired) {
            return $this->errorResponse('This is not expired listing, you only allow to relist expired listing', 422,);
        }

        /**
         * Check if listing > 60, if so charge relist fee
         */
        $currentDate = Carbon::now()->subDays(60);
        $isExpired = Listing::where('id', $request->listing_id)
            ->where('created_at', '<', $currentDate)->exists();
        //Check if user have enough point to perform this action when ad listing > 60
        if (!app(PointDeductionController::class)->validate_user_point($user->id, 'relist_ad') && $isExpired) {
            return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
        }
        /**
         * Now reset this listing
         * - Base on duration_of_auction update start_date and end_date
         * - Set listing_status to published
         * - Reset is_highlight=0, is_feature=0, feature_end_date = null
         * - and update updated_at date
         * Following is reset other data
         * - Delete dats from listing_activity for this lsiting_id
         * - Delete data from listing_auction_bid for this lsiting_id
         * - Delete data from listing_auction_bid_auto for this lsiting_id
         * - Delete data from listing_question_answer and listing_question_answer_reply ( this two table are realted )
         * - Delete data from listing_user_activity for this lsiting_id
         * See function for implementation
         */
        return $this->reListUserListing($request->listing_id, $isExpired, $user->id);
    }


    protected function reListUserListing($listing_id, $isExpired, $user_id)
    {
        $currentDuration = Listing::where('id', $listing_id)->value('duration_of_auction');
        if ($currentDuration === "7") {
            $new_start_date = now();
            $new_end_date = now()->addDays(7);
        }
        if ($currentDuration === "14") {
            $new_start_date = now();
            $new_end_date = now()->addDays(14);
        }

        // We will use DB transaction incase anything goes wrong we roolback
        DB::beginTransaction();

        try {
            Listing::where('id', $listing_id)->update([
                'start_date' => $new_start_date,
                'end_date' => $new_end_date,
                'is_highlight' => 0,
                'is_feature' => 0,
                'listing_status' => 'published',
                'feature_end_date' => null,
                'updated_at' => now(),
                'list_relist_date' => now()
            ]);

            DB::table('listing_activity')->where('listing_id', $listing_id)->delete();
            DB::table('listing_auction_bid')->where('listing_id', $listing_id)->delete();
            DB::table('listing_auction_bid_auto')->where('listing_id', $listing_id)->delete();
            ListingQuestionAnswer::where('listing_id', $listing_id)->delete();
            if ($isExpired) {
                $point_to_deduct = app(PointDeductionController::class)->deduct_amount($user_id, 'relist_ad');
                DB::table('user_point_deduction_history')->insert([
                    'user_id' => $user_id,
                    'transaction_type' => 'relist_ad',
                    'deducted_point' => $point_to_deduct,
                    'other_details' => 'You relisted your ad successfully',
                    'listing_id' => $listing_id,
                ]);
            }

            DB::commit();
        } catch (Throwable $e) {

            DB::rollback();
            return $this->errorResponse("Something went wrong while relisting, please try again");
        }

        return $this->successResponse(true, 'Successfully relisted your listing');
    }

    public function sendEmailforLisitingClassified(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'name' => 'required',
            "mobile_no" => 'required',
            "email" => 'required|email',
            "list_id" => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422,);
        }

        $seller_email = Listing::join('users', 'user_id', '=', 'users.id')->where('listing.id', $request->list_id)->value('users.email');

        $email_me_copy = false;
        if ($request->email_me_copy) {
            $email_me_copy = true;
        }

        $listing = Listing::select('manufacture_year', 'car_make_name', 'model', 'variant', 'asking_price')->where('id', $request->list_id)->first();
        //Get image src from helper
        $image_src = ListingRelated::getImageForListing($request->list_id);

        $data = [
            "message" => $request->message,
            "name" => $request->name,
            "mobile_no" => $request->mobile_no,
            "email" => $request->email,
            "seller_email" => $seller_email,
            "email_me_copy" => $email_me_copy,
            "year" => $listing->manufacture_year,
            "make" => $listing->car_make_name,
            "model" => $listing->model,
            "variant" => $listing->variant,
            "image_src" => $image_src,
            "asking_price" => $listing->asking_price
        ];

        dispatch(new SendEmailToSellerForClassified($data));
        return $this->successResponse(true, "Email Sent to seller");
    }

    public function markOrUnmarkAsFavourite(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            "list_id" => 'required',
            "is_favorite" => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422,);
        }

        $dataArray = [
            "is_favorite" => $request->is_favorite,
            "listing_id" => $request->list_id,
            "user_id" => $user->id
        ];

        $is_exist_activity_row = ListingUserActivity::where('listing_id', $request->list_id)->where('user_id', $user->id)->first();

        if (is_null($is_exist_activity_row)) {
            ListingUserActivity::create($dataArray);
            return $this->successResponse(true, "Saved as your favorite listing");
        }

        ListingUserActivity::where('listing_id', $request->list_id)->where('user_id', $user->id)->update([
            "is_favorite" => $request->is_favorite
        ]);

        return $this->successResponse(true, "Updated your choice for this listing");
    }

    public function listViewCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $isExist = DB::table('listing_activity')->where('listing_id', $request->listing_id)->first();

        if ($isExist !== null) {
            $currentViewCount = DB::table('listing_activity')->where('listing_id', $request->listing_id)->value('view_count');
            DB::table('listing_activity')->where('listing_id', $request->listing_id)->update([
                'view_count' => ++$currentViewCount
            ]);
        } else {
            DB::table('listing_activity')->insert([
                'view_count' => 1,
                'listing_id' => $request->listing_id
            ]);
        }
    }

    public function otherListingMayUserLike()
    {
        $randomLisiting = Listing::with('listing_auction_bid:listing_id,bid_amount as current_bid_amount')->inRandomOrder()
            ->where('listing_status', 'published')
            ->limit(9)
            ->get(['id', 'car_images', 'listing_type', 'ad_title', 'area', 'state', 'car_make_name', 'model', 'manufacture_year', 'transmission', 'fuel_type', 'starting_price', 'asking_price', 'buy_now_price', 'reserve_price','categories_id','car_plate_number','is_highlight']);

        return $this->successResponse($randomLisiting);
    }

    public function popularListing($categories_id)
    {
        $popularlisting = Listing::with('listing_auction_bid:listing_id,bid_amount as current_bid_amount')->join('listing_activity', 'listing_activity.listing_id', '=', 'listing.id')
            ->where('listing.listing_status', 'published')
            ->where('categories_id', $categories_id)
            ->orderBy('listing_activity.view_count', 'desc')
            ->limit(9)
            ->get(['listing.id', 'listing.car_images as car_images', 'listing.ad_title', 'listing.listing_type', 'listing.area', 'listing.state', 'listing.car_make_name', 'listing.model', 'listing.manufacture_year', 'listing.transmission', 'listing.fuel_type', 'listing.starting_price', 'listing.asking_price', 'listing.buy_now_price', 'listing.reserve_price','listing.categories_id','listing.car_plate_number','listing.is_highlight']);

        return $this->successResponse($popularlisting);
    }

    public function recentlyAdded()
    {
        $recentlyAdded = Listing::with('listing_auction_bid:listing_id,bid_amount as current_bid_amount')->orderBy('id', 'desc')
            ->where('listing_status', 'published')
            ->limit(9)
            ->get(['id', 'car_images', 'listing_type', 'area', 'ad_title', 'state', 'car_make_name', 'model', 'manufacture_year', 'transmission', 'fuel_type', 'starting_price', 'asking_price', 'buy_now_price', 'reserve_price','categories_id','car_plate_number','is_highlight']);

        return $this->successResponse($recentlyAdded);
    }

    public function auctionClosingSoon($categories_id)
    {
        $auctionClosingSoon = Listing::with('listing_auction_bid:listing_id,bid_amount as current_bid_amount')->orderBy('end_date', 'asc')
            ->where('listing_status', 'published')
            ->where('listing_type', 'auction')
            ->where('categories_id', $categories_id)
            ->limit(9)
            ->get(['id', 'car_images', 'listing_type', 'area', 'ad_title', 'state', 'car_make_name', 'model', 'manufacture_year', 'transmission', 'fuel_type', 'starting_price', 'asking_price', 'buy_now_price', 'reserve_price', 'end_date','categories_id','car_plate_number','is_highlight']);
            return $this->successResponse($auctionClosingSoon);
        }

        public function ifShowContactDeductPoint(Request $request, $seller_info_or_buyer_info)
        {
            $user = Auth::user();
            $action_name = $seller_info_or_buyer_info === 'seller_info' ? 'check_seller_info' : 'check_buyer_info_by_seller';

            $validator = Validator::make($request->all(), [
                "listing_id" => 'required',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }
            $listing_id = $request->input('listing_id');
            $checkIsUserAlreadyPaidForThisListing = UserPointDeductionHistory::where('user_id', $user->id)
                ->where('listing_id', $listing_id)->where('transaction_type', $action_name)->exists();

            if ($checkIsUserAlreadyPaidForThisListing) {
                return $this->errorResponse('You are already paid for this, you can view the contact info! if issue persist, please contact with admin', 422);
            }

            //Check if user have enough point to perform this action
            if (!app(PointDeductionController::class)->validate_user_point($user->id, $action_name)) {
                return $this->errorResponse('Not enough points to perform this action, please topup', 422,);
            }

            app(PointDeductionController::class)->deduct_point_from_user($user->id, $action_name, $listing_id);

            return $this->successResponse('true');
        }

        public function getTotalPublishedAds(Request $request)
        {
            $total = Listing::where('listing_status', 'published')->count();
            return $this->successResponse($total);
    }
}
