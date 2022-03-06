<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailWhenUserSubmitInsuranceForm;
use App\Jobs\SendEmailWhenUserSubmitExtendWarrantyForm;
use App\Jobs\SendEmailWhenUserSubmitCarLoanForm;

class FinanceInsurenceController extends ApiController
{
    public function submitCarLoanForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_no' => 'required',
            "email" => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $data = [
            "bank_name" => $request->bank_name,
            "car_price" => $request->car_price,
            "down_payment" => $request->down_payment,
            "loan_amount" => $request->loan_amount,
            "bank_rate" => $request->bank_rate,
            "payment_term" => $request->payment_term,
            "monthly_repayment" => $request->monthly_repayment,
            "car_condition" => $request->car_condition,
            "car_origin" => $request->car_origin,
            //User details
            "name" => $request->name,
            "mobile_no" => $request->mobile_no,
            "email" => $request->email,
            "car_make" => $request->car_make,
            "year" => $request->year,
        ];

        dispatch(new SendEmailWhenUserSubmitCarLoanForm($data));

        return $this->successResponse(true, "Succcessfully sent email");
    }

    public function submitExtendWarrantyForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_no' => 'required',
            "email" => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $data = [
            "name" => $request->name,
            "mobile_no" => $request->mobile_no,
            "email" => $request->email,
            "car_condition" => $request->car_condition,
            "car_make" => $request->car_make,
            "car_model" => $request->car_model,
            "year" => $request->year,
        ];

        dispatch(new SendEmailWhenUserSubmitExtendWarrantyForm($data));

        return $this->successResponse(true, "Succcessfully sent email");
    }

    public function submitInsuranceForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_no' => 'required',
            "email" => 'required',
            "driving_year" => 'required',
            "no_of_claims_in_past_three_years" => 'required',
            "gender" => 'required',
            "marital_status" => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $data = [
            "name" => $request->name,
            "mobile_no" => $request->mobile_no,
            "email" => $request->email,
            "born_in" => $request->born_in,
            "driving_year" => $request->driving_year,
            "no_of_claims_in_past_three_years" => $request->no_of_claims_in_past_three_years,
            "ncd" => $request->ncd,
            "car_make" => $request->car_make,
            "car_model" => $request->car_model,
            "gender" => $request->gender,
            "marital_status" => $request->marital_status,
        ];

        dispatch(new SendEmailWhenUserSubmitInsuranceForm($data));

        return $this->successResponse(true, "Succcessfully sent email");
    }
}
