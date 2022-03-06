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

class PointsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function userpointsystem(){

    	$point_system_list=DB::table("user_point_setting")->where("point_category","private")->get()->toArray();

        return view('pages.points_management.user-points-system')
        	   ->with("point_system_list",$point_system_list);
    }

    public function dealerpointsystem(){

    	$point_system_list=DB::table("user_point_setting")->where("point_category","dealer")->get()->toArray();

        return view('pages.points_management.dealer-points-system')
        	   ->with("point_system_list",$point_system_list);
    }

    public function agentpointsystem(){

    	$point_system_list=DB::table("user_point_setting")->where("point_category","broker_agent")->get()->toArray();

        return view('pages.points_management.agent-points-system')
        	   ->with("point_system_list",$point_system_list);
    }

    public function editPoint(Request $request){
    	$transaction_id=$request->id;
    	$point=$request->point;

    	DB::table("user_point_setting")->where("id",$transaction_id)->update(["deduction_point"=>$point]);
    }

}
