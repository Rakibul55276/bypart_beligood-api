<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

class DirectoryController extends Controller{

	public function __construct() {
        $this->middleware('auth');
        $date = new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur'));
        $this->currentdate = $date->format('Y-m-d H:i:s');

    }

    public function categories(){

    	$list_of_categories=DB::table("directory_category")->get()->toArray();

    	return view('pages.directory_management.directory-category')
    		   ->with("list_of_categories",$list_of_categories);
    }

    public function addCategory(Request $request){
    	$category_name=$request->category_name;

    	if(!empty($category_name)){
    		DB::table("directory_category")->insert(['category_name'=>$category_name]);
    	}
    }

    public function editCategory(Request $request){
    	$category_name=$request->category_name;
    	$category_id=$request->category_id;

    	if(!empty($category_name)){
    		DB::table("directory_category")->where("id",$category_id)->update(['category_name'=>$category_name]);
    	}
    }

    public function subcategories(){

    	$ref_id=isset($_GET['ref'])?$_GET['ref']:0;

    	$category_name=DB::table("directory_category")->where("id",$ref_id)->value("category_name");

    	$list_of_sub_categories=DB::table("directory_sub_category")->where("directory_category_id",$ref_id)->get()->toArray();

    	return view('pages.directory_management.directory-sub-category')
    		   ->with("category_name",$category_name)
    		   ->with("category_id",$ref_id)
    		   ->with("list_of_sub_categories",$list_of_sub_categories);
    }

    public function addSubCategory(Request $request){
    	$category_name=$request->category_name;
    	$directory_category_id=$request->directory_category_id;

    	if(!empty($category_name)){
    		DB::table("directory_sub_category")->insert(['sub_category_name'=>$category_name,"directory_category_id"=>$directory_category_id]);
    	}
    }

    public function editSubCategory(Request $request){
    	$category_name=$request->sub_category_name;
    	$category_id=$request->sub_category_id;

    	if(!empty($category_name)){
    		DB::table("directory_sub_category")->where("id",$category_id)->update(['sub_category_name'=>$category_name]);
    	}
    }

    public function updateIcon(Request $request){
        $id=$request->sub_category_id;

        if(isset($request->upload_icon)){
            $filePath = Storage::disk('digitalocean')->putFile('adminuploads', $request->upload_icon, 'public');
            $getUrl = Storage::disk('digitalocean')->url($filePath);
            
            DB::table("directory_sub_category")->where("id",$id)->update(['sub_category_icon'=>$getUrl]);
        }
    }

    public function companies(){

    	$ref_id=isset($_GET['ref'])?$_GET['ref']:0;

    	$sub_category_name=DB::table("directory_sub_category")->where("id",$ref_id)->value("sub_category_name");

    	return view('pages.directory_management.directory-companies')
    		   ->with("sub_category_id",$ref_id)
    		   ->with("sub_category_name",$sub_category_name);
    }

    public function getCompanies(Request $request){

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
            $search_str="(company_name LIKE '%".$searchValue."%' OR company_email LIKE '%".$searchValue."%' OR company_phone_number LIKE '%".$searchValue."%' OR address LIKE '%".$searchValue."%' OR city LIKE '%".$searchValue."%' OR state LIKE '%".$searchValue."%')";
        }

        $sub_category_id=$request->sub_category_id;
        $company_ids=DB::table("directory_com_to_sub_cat")->where("dir_sub_category_id",$sub_category_id)->get()->toArray();
        $company_ids=array_column(json_decode(json_encode($company_ids)), "dir_company_info_id");

        if(empty($company_ids)){
        	$company_ids=array(0);
        }

        // Total records
        $totalRecords = DB::table("directory_company_info")->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table("directory_company_info")->select('count(*) as allcount')->whereIn("id",$company_ids)->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("directory_company_info")->orderBy($columnName,$columnSortOrder)
            ->whereRaw($search_str)
            ->whereIn("id",$company_ids)
            ->select('directory_company_info.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;

            $edit_logo_html='<button class="btn btn-secondary" onclick="editLogo(\''.$record->id.'\',\''.$record->company_name.'\');" >EDIT LOGO</button><br><br>';

            $edit_button_html='<button class="btn btn-secondary" onclick="editCompanyModal(\''.$record->id.'\');">EDIT COMPANY</button>';

            
            // $delete_button_html='<span onclick="deleteCompany(\''.$record->id.'\');"><i class="material-icons" style="color:red; cursor: pointer; padding:5px;">delete</i></span>';
            
            

            $action_html=$edit_logo_html." ".$edit_button_html;

            $recommended_text=($record->is_recommended==1)?"True":"False";
            $premium_text=($record->is_premium==1)?"True":"False";

            $logo="-";
            if(!empty($record->company_logo)){
            	$logo='<a href="'.$record->company_logo.'" target="_blank">View Logo</a>';
            }

            $url="-";
            if(!empty($record->company_url)){
            	$url='<a href="'.$record->company_url.'" target="_blank">Click Here</a>';
            }


            $data_arr[] = array(
                 'id' => $id,
                 'company_name'=>$record->company_name,
				 'company_description'=>$record->company_description,
				 'company_logo'=>$record->company_logo,
				 'company_email'=>$record->company_email,
				 'company_phone_number'=>$record->company_phone_number,
				 'address'=>$record->address,
				 'city'=>$record->city,
				 'state'=>$record->state,
				 'is_recommended_text'=>$recommended_text,
				 'is_premium_text'=>$premium_text,

				 'url'=>$url,
				 'logo'=>$logo,

				 'is_recommended'=>$record->is_recommended,
				 'is_premium'=>$record->is_premium,
				 "action_html"=>$action_html
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

    public function editCompanyModal(Request $request){
    	$company_id=$request->company_id;

    	$company_info=DB::table("directory_company_info")->where("id",$company_id)->get()->toArray();

    	return view('pages.directory_management.company-modal-edit')
    		   ->with("company_id",$company_id)
    		   ->with("company_info",$company_info);
    }

    public function uploadLogo(Request $request){
        $id=$request->id;

        if(isset($request->upload_logo)){
            $filePath = Storage::disk('digitalocean')->putFile('adminuploads', $request->upload_logo, 'public');
            $getUrl = Storage::disk('digitalocean')->url($filePath);
            
            DB::table("directory_company_info")->where("id",$id)->update(['company_logo'=>$getUrl]);
        }
    }

    public function editCompany(Request $request){
    	$params=$request->all();

    	$id=$request->id;

    	$updateArray=array();
    	foreach ($params as $key => $value) {
    		if($key!='id'){
    			$updateArray[$key]=$value;
    		}
    	}

    	 DB::table("directory_company_info")->where("id",$id)->update($updateArray);
    }

    public function addCompanyModal(){

    	return view('pages.directory_management.company-modal-add');
    }

    public function addCompany(Request $request){
    	$params=$request->all();

        $insertArray=array();
        foreach ($params as $key => $value) {
        	if($key!='sub_category_id'){
        		$insertArray[$key]=$value;
        	}
        }

        $company_id=DB::table("directory_company_info")->insertGetId($insertArray);

        $sub_category_id=$request->sub_category_id;

        DB::table("directory_com_to_sub_cat")
        	->insert([
        		"dir_sub_category_id"=>$sub_category_id,
        		"dir_company_info_id"=>$company_id
        	]);
    }
    
}
