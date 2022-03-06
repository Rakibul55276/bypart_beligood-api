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

class MakeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function makeListing(){

    	return view('pages.makes-models');
    }

    public function makeRequests(){

        return view('pages.makes-requests');
    }

    public function getMakeRequests(Request $request){

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
            $search_str="(make LIKE '%".$searchValue."%' OR model LIKE '%".$searchValue."%' OR variant LIKE '%".$searchValue."%' OR fuel_type LIKE '%".$searchValue."%')";
        }

        // Total records
        $totalRecords = DB::table("car_make_requsts")->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table("car_make_requsts")->select('count(*) as allcount')->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("car_make_requsts")->orderBy($columnName,$columnSortOrder)
            ->whereRaw($search_str)
            ->select('car_make_requsts.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();
            
        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;

            $document="-";
            if(!empty($record->supporting_document)){
                $document='<a href="'.$record->supporting_document.'" target="_blank">Preview</a>';
            }

            $delete_button_html='<span onclick="deleteRequest(\''.$record->id.'\');"><i class="material-icons" style="color:red; cursor: pointer; padding:5px;">delete</i></span>';
            
            $data_arr[] = array(
                'id' =>$id,
                'make' =>$record->make,
                'model' =>$record->model,
                'variant' =>$record->variant,
                'fuel_type' =>$record->fuel_type,
                'manufactured_year' =>$record->manufactured_year,
                'transmission' =>$record->transmission,
                'engine_capacity' =>$record->engine_capacity,
                'condition' =>$record->condition,
                'action'=>$delete_button_html,
                'supporting_document'=>$document
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

    public function deleteRequest(Request $request){
        $make_id=$request->get('make_id',0);

        DB::table("car_make_requsts")->where("id",$make_id)->delete();
    }

    public function getMakesModels(Request $request){

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
            $search_str="(make LIKE '%".$searchValue."%' OR model_name LIKE '%".$searchValue."%' OR variant LIKE '%".$searchValue."%' OR generation LIKE '%".$searchValue."%' OR fuel_type LIKE '%".$searchValue."%' OR car_body_type LIKE '%".$searchValue."%' OR engine_size LIKE '%".$searchValue."%' OR engine_code LIKE '%".$searchValue."%')";
        }

        // Total records
        $totalRecords = DB::table("car_make")->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table("car_make")->select('count(*) as allcount')->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("car_make")->orderBy($columnName,$columnSortOrder)
            ->whereRaw($search_str)
            ->select('car_make.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;

            $edit_button_html='<span onclick="editModal(\''.$record->id.'\');"><i class="material-icons" style="color:grey; cursor: pointer; padding:5px;">create</i></span>';


            $data_arr[] = array(
	            'id' =>$id,
				'make' =>$record->make,
				'model_name' =>$record->model_name,
				'variant' =>$record->variant,
				'generation' =>$record->generation,
				'min_year' =>$record->min_year,
				'max_year' =>$record->max_year,
				'fuel_type' =>$record->fuel_type,
				'car_body_type' =>$record->car_body_type,
				'door' =>$record->door,
				'seat' =>$record->seat,
				'engine_size' =>$record->engine_size,
				'engine_code' =>$record->engine_code,
				'action_html' => $edit_button_html
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

    public function editModalData(Request $request){
    	$id=$request->id;

    	$records = DB::table("car_make")->where("id",$id)->get()->toArray();

    	echo json_encode($records);
        exit;
    }

    public function addMakes(Request $request){
        $params=$request->all();
        /**
         * Following to generate data for min_max_year
         */
        $min_year = $request->min_year;
        $max_year = $request->max_year;

        $min_year = $min_year === null ? '1970' : $min_year;
        $max_year = $max_year == "" ? date('Y') : $max_year;
        $year = range($min_year, $max_year);
        $year_to_insert = implode(',', $year);

        $insertArray=array();
        foreach ($params as $key => $value) {
                $insertArray[$key]=$value;
        }
        $insertArray['min_max_year'] =  $year_to_insert;

         DB::table("car_make")->insert($insertArray);
    }

    public function editMakes(Request $request){
    	$params=$request->all();

    	$id=$request->id;

        /**
         * Following to generate data for min_max_year
         */
        $min_year = $request->min_year;
        $max_year = $request->max_year;

        $min_year = $min_year === null ? '1970' : $min_year;
        $max_year = $max_year == "" ? date('Y') : $max_year;
        $year = range($min_year, $max_year);
        $year_to_insert = implode(',', $year);

    	$updateArray=array();
    	foreach ($params as $key => $value) {
    		if($key!='id'){
    			$updateArray[$key]=$value;
    		}
    	}
        $updateArray['min_max_year'] =  $year_to_insert;

    	DB::table("car_make")->where("id",$id)->update($updateArray);
    }
}
