<?php

namespace App\Http\Controllers\Api\Payment;
use JagdishJP\FpxPayment\Fpx;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FpxController extends ApiController
{
    public function getBankList()
    {
        $banks = DB::table('banks')->where('status', 'online')->get();
        return $this->successResponse($banks);
    }

    public function getTransactionHistory(Request $request){

        $user_id=Auth::user()->id;
        $sort=isset($request->sort)?$request->sort:"desc"; //'asc' or 'desc'
        $filter_type=isset($request->filter_type)?$request->filter_type:"";//'all','released','payment','deducted'

        $page=isset($request->page)?$request->page:1;
        $limit=isset($request->limit)?$request->limit:25;

        $data=DB::table("user_point_transaction_history");

        if(!empty($filter_type)&&$filter_type!='all'){
            $data=$data->where("point_type",$filter_type);
        }

        $data=$data->where("user_id",$user_id);
        $data=$data->orderby("created_at",$sort);
        $data=$data->paginate($limit);

        foreach ($data as $key => $value) {
            if($value->point_type!='payment' && $value->point_type!='released'){
                $point_count=(int)$value->point_count;
                $value->point_count="-".(string)$point_count;
            }

            if($value->point_type=='payment'){
                if($value->status=='succeeded'){
                    $value->description="Purchase ".$value->point_count." pts at RM ".$value->point_count;
                }else{
                    $value->description="*Declined Purchase ".$value->point_count." pts at RM ".$value->point_count;
                }
            }

            if($value->point_type=='released'){
                $description=str_replace("lock","released",$value->description);
                $description=str_replace("since you are","for",$description);

                $value->description="^".$description;
            }
        }
        
        return $this->successResponse($data);
    }

    public function callback(Request $request) {
		$response = $request->handle();
		if ($response['response_format'] == 'JSON')
		return response()->json(['response' => $response, 'fpx_response' => $request->all()]);

		// Update your order status
	}
}
