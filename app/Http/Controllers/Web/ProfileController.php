<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\ApiController;
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

class ProfileController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        return view('pages.admin-profile');
    }


    public function notifications(Request $request){

        $admin_id=Auth::user()->id;

        $page=$request->get('page',1);
        $limit=$request->get('limit',30);
        $offset=($page-1)*$limit;

        $notifications=DB::table("notifications")->where("user_id",$admin_id)->orderBy("is_seen","asc")->orderBy("created_at","desc")->limit($limit)->offset($offset)->get()->toArray();

        $notifications_count=DB::table("notifications")->where("user_id",$admin_id)->where("is_seen",0)->count();

        $return['count']=$notifications_count;
        $return['data']=$notifications;

        return $this->successResponse($return);
    }

    public function allnotifications(){
        return view('pages.notification-list');
    }

    public function userprofile(Request $request){

    	$user_id=$request->id;

    	$user_details=DB::table("users")->where("id",$user_id)->get()->toArray();

        $user_status=array('approve'=>'Approve','deactive'=>'Deactive','banned'=>'Banned','deleted'=>'Deleted');
        $admin_status=array('approve'=>'Approve','dis_approve'=>'Dis Approve','under_review'=>'Under Review');

        $ssm_files=array();
        $name_cards=array();
        $premise_images=array();
        $other_supprting_docs=array();

        $company_info=array();

        if($user_details[0]->user_type=='agent'||$user_details[0]->user_type=='dealer'){
            $company_info=DB::table("companies")->where("id",$user_details[0]->company_id)->get()->toArray();

            if(is_array(json_decode($company_info[0]->company_cert_ssm_file))){
                $ssm_files_obj=json_decode($company_info[0]->company_cert_ssm_file);
                foreach ($ssm_files_obj as $kssm => $vssm) {
                    if(!empty($vssm->url)){
                        $ssm_files[]=$vssm->url;
                    }
                }
            }

            if(is_array(json_decode($company_info[0]->name_card_file))){
                $name_cards_obj=json_decode($company_info[0]->name_card_file);
                foreach ($name_cards_obj as $kname_cards => $vname_cards) {
                    if(!empty($vname_cards->url)){
                        $name_cards[]=$vname_cards->url;
                    }
                }
            }

            if(is_array(json_decode($company_info[0]->premise_pictures))){
                $premise_images_obj=json_decode($company_info[0]->premise_pictures);
                foreach ($premise_images_obj as $kpremise_images => $vpremise_images) {
                    if(!empty($vpremise_images->url)){
                        $premise_images[]=$vpremise_images->url;
                    }
                }
            }

            if(is_array(json_decode($company_info[0]->other_supporting_files))){
                $other_supprting_docs_obj=json_decode($company_info[0]->other_supporting_files);
                foreach ($other_supprting_docs_obj as $kother => $vother) {
                    if(!empty($vother->url)){
                        $other_supprting_docs[]=$vother->url;
                    }
                }
            }
        }

    	if(!empty($user_details)){
    		return view('pages.users_management.user-profile')
               ->with("user_status",$user_status)
               ->with("admin_status",$admin_status)
               ->with("ssm_files",$ssm_files)
               ->with("name_cards",$name_cards)
               ->with("premise_images",$premise_images)
               ->with("other_supprting_docs",$other_supprting_docs)
               ->with("company_info",$company_info)
        	   ->with("user_details",$user_details);
        	}else{
        		echo "No Such User Found."; exit;
        	}

    }

    public function userview(Request $request){

        $user_id=$request->id;

        $user_details=DB::table("users")->where("id",$user_id)->get()->toArray();

        if(!empty($user_details)){
            return view('pages.users_management.readonly-user-profile')
               ->with("user_details",$user_details);
            }else{
                echo "No Such User Found."; exit;
            }

    }

    public function updateUser(Request $request){

    	$user_id=$request->user_id;

    	$updateArray=array();
    	$updateArray['first_name']=$request->first_name;
    	$updateArray['last_name']=$request->last_name;
    	$updateArray['email']=$request->email;
    	$updateArray['mobile_no']=$request->mobile_no;
    	$updateArray['address']=$request->address;
    	$updateArray['city']=$request->city;
    	$updateArray['state']=$request->state;
        $updateArray['country']=$request->country;
        $updateArray['status']=$request->status;
    	$updateArray['status_admin']=$request->status_admin;
        $updateArray['postal_code']=$request->postal_code;
        $updateArray['bp_point']=$request->bp_point;

    	if(!empty($user_id)){
    		$upadte=DB::table("users")->where("id",$user_id)->update($updateArray);
    	}



    }

    public function updateCompany(Request $request){
        $params=$request->all();

        $id=$request->id;

        $updateArray=array();
        foreach ($params as $key => $value) {
            if($key!='id'){
                $updateArray[$key]=$value;
            }
        }

         DB::table("companies")->where("id",$id)->update($updateArray);
    }

    public function newuser(){

        return view('pages.users_management.add-user-profile');
    }

    public function addNewUser(Request $request){

        $insertArray=array();
        $insertArray['first_name']=$request->first_name;
        $insertArray['last_name']=$request->last_name;
        $insertArray['email']=$request->email;
        $insertArray['password']=bcrypt($request->password);
        $insertArray['mobile_no']=$request->mobile_no;
        $insertArray['address']=$request->address;
        $insertArray['city']=$request->city;
        $insertArray['state']=$request->state;
        $insertArray['country']=$request->country;
        $updateArray['user_type']='admin';
        $insertArray['postal_code']=$request->postal_code;

        DB::table("users")->insert($insertArray);




    }



    public function changePassword(Request $request){

    	$current_password=$request->current_password;
    	$new_password=$request->new_password;
    	$user_id=$request->user_id;

    	$existing_password=DB::table("users")->where("id",$user_id)->value("password");

    	$pasword_to_compare=bcrypt($current_password);

    	$status=400;
    	if($existing_password==$pasword_to_compare){
    		$status=200;

    		$update_password=DB::table("users")->where("id",$user_id)->update(["password"=>bcrypt($new_password)]);
    	}

    	$returnArray=array("status"=>$status);
    	echo json_encode($returnArray);
        exit;

    }

    public function users(Request $request){


    	return view('pages.users_management.private-users');
    }

    public function agents(Request $request){


    	return view('pages.users_management.agents');
    }

    public function dealer(Request $request){


    	return view('pages.users_management.dealers');
    }

    public function admin(Request $request){


        return view('pages.users_management.admin');
    }

    public function getUsers(Request $request){

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
            $search_str="(first_name LIKE '%".$searchValue."%' OR last_name LIKE '%".$searchValue."%' OR email LIKE '%".$searchValue."%' OR address LIKE '%".$searchValue."%')";
        }

        $user_type=$request->user_type;

        // Total records
        $totalRecords = DB::table("users")->select('count(*) as allcount')->where("user_type",$user_type)->count();
        $totalRecordswithFilter = DB::table("users")->select('count(*) as allcount')->where("user_type",$user_type)->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("users")->orderBy($columnName,$columnSortOrder)
        	->where("user_type",$user_type)
            ->whereRaw($search_str)
            ->select('users.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;
            $username = $record->first_name." ".$record->last_name;
            $mobile_no = $record->mobile_no;
            $address = isset($record->address)?$record->address:" No Address ";
            $email = $record->email;

            $edit_button_html='<span onclick="editUser(\''.$record->id.'\');"><i class="material-icons" style="color:grey; cursor: pointer; padding:5px;">create</i></span>';

            if($record->status=='deleted'){
            	$delete_button_html='<span onclick="restoreUsers(\''.$record->id.'\');"><i class="material-icons" style="color:green; cursor: pointer; padding:5px;">restore</i></span>';
            }else{
            	$delete_button_html='<span onclick="deleteUsers(\''.$record->id.'\');"><i class="material-icons" style="color:red; cursor: pointer; padding:5px;">delete</i></span>';
            }

            $status_admin=ucwords(str_replace("_", " ", $record->status_admin));

            $action_html=$edit_button_html." ".$delete_button_html;

            $data_arr[] = array(
                "id" => $id,
                "first_name" => $username,
                "mobile_no" => $mobile_no,
                "address" => $address,
                "email" => $email,
                "status_admin"=>$status_admin,
                "action_html" => $action_html
            );

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

    public function deleteUsers(Request $request){
    	$user_id=$request->id;

    	$change_status=DB::table("users")->where("id",$user_id)->update(["status"=>"deleted"]);
    }

    public function restoreUsers(Request $request){
    	$user_id=$request->id;

    	$change_status=DB::table("users")->where("id",$user_id)->update(["status"=>"approve"]);
    }
}
