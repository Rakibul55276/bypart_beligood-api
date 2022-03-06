<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Notification\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use ClientException;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;
use App\Models\Listing\Listing;

class ListingController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
        $date = new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur'));
        $this->currentdate = $date->format('Y-m-d H:i:s');

    }

    public function index(){

        $listing_id=isset($_GET['ref'])?$_GET['ref']:"";
        $listing_id=isset($_POST['ref'])?$_POST['ref']:$listing_id;

        if(!empty($listing_id)){
            $listing_details=DB::table("listing")
                            ->select('listing.*','users.id as user_id','users.first_name','users.last_name','users.avatar','users.user_type','users.email','users.mobile_no','users.status_admin')
                            ->leftjoin('users','listing.user_id','=','users.id')
                            ->where("listing.id",$listing_id)
                            ->get()->toArray();
            foreach ($listing_details as $key => $value) {

                $images=is_array(json_decode($value->car_images,true))?array_values(json_decode($value->car_images,true)):array();

                $exclude=array(null,'');
                $images = array_values(array_diff($images, $exclude));

                $listing_details[$key]->images=$images;

                foreach ($value as $k => $v) {
                    if(!is_array($v)){
                        $value->$k=ucfirst($v);
                    }
                }
            }

            return view('pages.listing_management.listing-details')
                   ->with('listing_details',$listing_details)
                   ->with('currentdate',$this->currentdate);
        }else{

            header('Location:'.config('bypart.admin_url'));
            exit;
        }
    }

    public function classified(){

        return view('pages.listing_management.classified-listing');
    }

    public function auction(){

        return view('pages.listing_management.auction-listing');
    }

    public function getListing(Request $request){

    	$draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $search_str="(1=1)";
        if(!empty($searchValue)){
            $search_str="(listing.state LIKE '%".$searchValue."%' OR listing.listing_status LIKE '%".$searchValue."%' OR car_make_name LIKE '%".$searchValue."%' OR first_name LIKE '%".$searchValue."%' OR last_name LIKE '%".$searchValue."%' OR car_condition LIKE '%".$searchValue."%' OR model LIKE '%".$searchValue."%')";
        }

        $listing_type=$request->listing_type;

        // Total records
        $totalRecords = DB::table("listing")->select('count(*) as allcount')->where("listing_type",$listing_type)->count();
        $totalRecordswithFilter = DB::table("listing")->leftjoin('users','listing.user_id','=','users.id')->select('count(*) as allcount')->where("listing_type",$listing_type)->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("listing")->orderBy($columnName,$columnSortOrder)
        	->select('listing.*','listing.car_make_name as car_name','users.first_name','users.last_name')
        	->leftjoin('users','listing.user_id','=','users.id')
        	->where("listing_type",$listing_type)
            ->whereRaw($search_str)
            ->orderBy('listing.created_at','desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;
            $username = $record->first_name." ".$record->last_name;
            $id_len=strlen((string)$record->id);
            if($id_len<6){
            	$zero_to_add=6-$id_len;

            	$listing_id="BPM";

            	for ($i=0; $i <$zero_to_add ; $i++) {
            		$listing_id=$listing_id."0";
            	}

            	$listing_id=$listing_id.$record->id;
            }else{
            	$listing_id="BPM".$record->id;
            }

            $car_condition=ucfirst($record->car_condition);
            $car_make=$record->car_name;
            $car_model=$record->model;
            $state=$record->state;
            $asking_price=$record->asking_price;
            $status=ucfirst(str_replace("_"," ",$record->listing_status));

            $view_more_button='<div><a href="'.config('bypart.admin_url').'/viewlisting?ref='.$record->id.'" target="_blank">View Details</a></div>';

            if($listing_type=='classified'){
            	$data_arr[] = array(
	                "id" => $id,
	                "listing_id" => $listing_id,
	                "car_condition" => $car_condition,
	                "car_make_name" => $car_make,
	                "model" => $car_model,
	                "state" => $state,
	                "asking_price" => $asking_price,
	                "first_name" => $username,
	                "listing_status" => $status,
                    "created_at" => $record->created_at,
	                "view_more_button" => $view_more_button,
	            );
            }else{
            	$starting_price=$record->starting_price;
            	$reserve_price=$record->reserve_price;
            	$buy_now_price=$record->buy_now_price;

            	$data_arr[] = array(
	                "id" => $id,
	                "listing_id" => $listing_id,
	                "car_condition" => $car_condition,
	                "car_make_name" => $car_make,
	                "model" => $car_model,
	                "state" => $state,
	                "starting_price" => $starting_price,
	                "reserve_price" => $reserve_price,
	                "buy_now_price" => $buy_now_price,
	                "first_name" => $username,
	                "listing_status" => $status,
	                "created_at" => $record->created_at,
                    "view_more_button" => $view_more_button,
	            );
            }

            $sno++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function updateListingStatus(Request $request){

        $listing_id=$request->listing_id;
        $status=$request->status;
        $rejection_reason=$request->rejection_reason;

        /**
         * Following to update end date and start date if its approveed
         */
        $isAuction = Listing::where('id', $listing_id)->where('listing_type', 'auction')->exists();
        $start_date = null;
        $end_date = null;
        if($isAuction && $status === 'published') {
            $auction_date = (int) Listing::where('id', $listing_id)->value('duration_of_auction');
            $start_date = now();
            //Temporary set to 4 hour
            $end_date = now()->addHours(4);
        }

        $filePath = null;
        $getUrl=null;
        if(isset($request->inspectionreport)) {
            $filePath = Storage::disk('digitalocean')->putFile('inspectionreport', $request->inspectionreport, 'public');
            $getUrl = Storage::disk('digitalocean')->url($filePath);
        }
        DB::table("listing")->where('id',$listing_id)->update([
            "listing_status" => $status,
            "rejection_reason" => $rejection_reason,
            "inspection_report" => $getUrl,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "updated_at" => now()
        ]);

        /**
         * Call Notification Global Saving API Start
         */
        if($status=='rejected'){
            $ad_title = Listing::where('id', $listing_id)->value('ad_title');

            $notification_header='Listing Rejected';
            $notification_content="An Admin has rejected your listing '".$ad_title."' Please click to View Details.";
            $notification_type='listing';
            $sender_id=Auth::user()->id;
            $user_ids=Listing::where('id',$listing_id)->pluck('user_id');

            $url = config('bypart.frontend_url') . 'pages/profile/my-ads/3';
            app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $user_ids, $listing_id);
        }
        /**
         * Call Notification Global Saving API End
         */
    }
}
