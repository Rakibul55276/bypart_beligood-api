<?php

namespace App\Http\Controllers;

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

use Illuminate\Support\Facades\Storage;

class CommonController extends ApiController
{
    public function __construct(){
        // $this->middleware('auth');
    }

    public function uploadFiles(Request $request){

    	$getUrl=null;
        if(isset($request->upload_file)) {
            $filePath = Storage::disk('digitalocean')->putFile('adminuploads', $request->upload_file, 'public');
            $getUrl = Storage::disk('digitalocean')->url($filePath);
        }

        $response = array(
            "status" => "success",
            "url" => $getUrl
        );

        echo json_encode($response);
        exit;
    }

    public function dynamicImagesSettings(){

    	$page_name_array=array("home"=>"Home","listing_details"=>"Listing Details","listing_filter"=>"Listing Filter","forum"=>"Forum","post_details"=>"Post Details","directory_categories"=>"Directory Categories","directory"=>"Directory");

    	$image_position_array=array("left"=>"Left","right"=>"Right","top"=>"Top","bottom"=>"Bottom");
    	$image_type_array=array("vertical"=>"Vertical","horizontal"=>"Horizontal");

    	$page_settings=DB::table("dynamic_image_settings")->get()->toArray();

    	return view("pages.dynamic-image-settings")
    		   ->with("page_name_array",$page_name_array)
    		   ->with("image_position_array",$image_position_array)
    		   ->with("image_type_array",$image_type_array)
    		   ->with("page_settings",$page_settings);
    }

    public function addSettings(Request $request){
    	$insertArray=array();

    	$insertArray['page_name']=$request->page_name;
    	$insertArray['image_type']=$request->image_type;
    	$insertArray['image_position']=$request->image_position;
    	$insertArray['image_redirect_url']=$request->redirect_url;

    	$insertArray['image_url']=null;
        if(isset($request->image_url)) {
            $filePath = Storage::disk('digitalocean')->putFile('adminuploads', $request->image_url, 'public');
            $insertArray['image_url'] = Storage::disk('digitalocean')->url($filePath);
        }

        DB::table("dynamic_image_settings")->insert($insertArray);
    }

    public function editSettings(Request $request){

    	$id=$request->id;

    	$updateArray=array();

    	$updateArray['page_name']=$request->page_name;
    	$updateArray['image_type']=$request->image_type;
    	$updateArray['image_position']=$request->image_position;
    	$updateArray['image_redirect_url']=$request->image_redirect_url;

        DB::table("dynamic_image_settings")->where("id",$id)->update($updateArray);
    }

    public function updateImage(Request $request){
        $id=$request->id;

        if(isset($request->upload_icon)){
            $filePath = Storage::disk('digitalocean')->putFile('adminuploads', $request->upload_icon, 'public');
            $getUrl = Storage::disk('digitalocean')->url($filePath);
            
            DB::table("dynamic_image_settings")->where("id",$id)->update(['image_url'=>$getUrl]);
        }
    }

    public function getDynamicSettings(Request $request){
    	$page_name=$request->page_name;

    	$settings=DB::table("dynamic_image_settings")->where("page_name",$page_name)->get()->toArray();

    	return $this->successResponse($settings);
    }

}
