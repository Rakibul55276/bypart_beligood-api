<?php

namespace App\Http\Controllers\FPX;

use JagdishJP\FpxPayment\Http\Requests\AuthorizationConfirmation as Request;
use App\Http\Controllers\Controller as BaseController;
use JagdishJP\FpxPayment\Fpx;
use App\Models\Payment\PaymentHistory;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class Controller extends ApiController {

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request) {

		$response = $request->handle();

		$status = $msg = '';
		// Lets create payment history since its successfull 
		if($response['status'] == 'succeeded') {
			PaymentHistory::create([
				'user_id' => $response['additional_params'],
				'amount' => $response['amount'],
				'reference_id' =>$response['reference_id'],
				'transaction_id' => $response['transaction_id'],
				'paydate' => $response['transaction_timestamp'],
				'status' => $response['status'],
				'message' => $response['message']
			]);
			//Lets add point to user account 
			User::where('id', $response['additional_params'])->update([
                'bp_point' => DB::raw('bp_point + ' . $response['amount'])
            ]);

			$status = 'success';
			$msg = $response['message'];
		} else {

			PaymentHistory::create([
				'user_id' => $response['additional_params'],
				'amount' => $response['amount'],
				'reference_id' =>$response['reference_id'],
				'transaction_id' => $response['transaction_id'],
				'paydate' => $response['transaction_timestamp'],
				'status' => $response['status'],
				'message' => $response['message']
			]);

			$status = $response['status'];
			$msg = $response['message'];

		}
		
		return redirect()->route('paymentdone')->with( [ 'status' => $status, 'msg' => $msg ] );
	}

	public function paymentDone()
	{
		$status = session()->get( 'status' );
		$msg = session()->get( 'msg' );
		
		$redirect_url = config('bypart.frontend_url').'pages/profile/top-up/1?status='.$status.'&msg='.$msg;
        return redirect($redirect_url);
		return 'ok';
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request) {
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function initiatePayment(Request $request, $iniated_from = 'web', $test = '') {
		// //additional_params -> should be user id send it from FE 
		$validator = Validator::make($request->all(), [
            'additional_params' => 'required',
            'customer_name' => 'required',
            'amount' => 'required',
			'customer_email' => 'required',
			'bank_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

		//Some extra staff 
		$isIdExist = User::where('email', $request->customer_email)->exists();
		
		if(!$isIdExist) {
			return $this->errorResponse('Invalid request, you are not registered user to the system to continue', 422);
		}

		$banks = Fpx::getBankList(true);
		$response_format =	$iniated_from == 'app' ? 'JSON' : 'HTML';

		return view('fpx-payment::payment', compact('banks', 'response_format', 'test', 'request'));
	}

}
