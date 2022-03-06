<?php

namespace App\Http\Controllers\Api;

use Mail;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendMobileTac;

use Throwable;

class AuthController extends ApiController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_no' => 'required|unique:users',
            'user_type' => 'required',
            'site_url' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        $email_verification_code = sha1(time());

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->mobile_no = $request->mobile_no;
        $user->email_verification_code = $email_verification_code;
        $user->user_type = $request->user_type;

        $isUseradded = $user->save();
        $userId = $user->id;

        if ($isUseradded) {
            // Add company if user is agent and dealer
            if ($request->user_type === "dealer" || $request->user_type === "agent") {
                $company = Company::create();
                User::where('id', $userId)->update(['company_id' => $company->id]);
            }

            $mailData = array(
                'site_url' => $request->site_url . "?token=" . $email_verification_code,
                'name' => $user->first_name . " " . $user->last_name,
                'email' => $user->email
            );
            //Lets send email
            $sendVerificationEmail = new EmailController();
            $sendVerificationEmail->sendVerificationEmail($mailData);
            return $this->successResponse(true, "Success, registration completed");
        }

        return $this->errorResponse("Something went wrong", 500);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::where(['password_reset_token' => $request->token])->first();;
        if ($user != null) {
            $user->password = Hash::make($request->password);
            $user->password_reset_token = NULL;
            $user->save();
            return $this->successResponse(true, "Password successfully updated!");
        }

        return $this->errorResponse("Token is invalid", 422);
    }

    public function notAuthenticated()
    {
        return $this->errorResponse("Not Authenticated", 422);
    }

    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $verification_code = $request->get('token');
        $user = User::where(['email_verification_code' => $verification_code])->first();

        if ($user != null) {
            if ($user->is_email_verified == 1) {
                return $this->successResponse(true, "Your email is already verified! You can login now");
            }

            $user->is_email_verified = 1;
            $user->email_verified_at = now();
            $user->status = "approve";
            $user->save();
            return $this->successResponse(true, "Verification successful, you can now login");
        }

        return $this->errorResponse("Invalid verification code!", 422);
    }

    public function sendAccountVerificationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'site_url' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::where(['email' => $request->get('email')])->first();

        if ($user != null) {
            if ($user->is_email_verified) {
                return $this->errorResponse("already-verified", 422);
            }

            $email_verification_code = sha1(time());
            $user->email_verification_code = $email_verification_code;
            $isUpdated = $user->save();

            if ($isUpdated) {
                $mailData = array(
                    'site_url' => $request->site_url . "?token=" . $email_verification_code,
                    'email' => $request->email,
                    "name" => $user->first_name . " " . $user->last_name
                );
                //Lets send email
                $sendVerificationEmail = new EmailController();
                $sendVerificationEmail->sendVerificationEmail($mailData);
                return $this->successResponse(true, 'Successfully sent account verification email');
            }

            return $this->errorResponse("Something went wrong! please try again", 422);
        }

        return $this->errorResponse("Not found", 422);
    }

    public function sentForgotPasswordEmailLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'site_url' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user = User::where(['email' => $request->get('email')])->first();

        // Do not sent reset password email if user signup with social login
        // We can check to if provider is not null
        $isUserHaveAccounnt = User::where('email', $request->get('email'))->where('provider','!=', NULL)->first();
        if($isUserHaveAccounnt !== NULL) {
            $getProviderName = User::where('email', $request->get('email'))->value('provider');
            return $this->errorResponse("Your account was created using social login, please use ". $getProviderName . " to sign in", 403);
        }

        if ($user != null) {
            $password_reset_token = sha1(time());
            $user->password_reset_token = $password_reset_token;
            $isUpdated = $user->save();

            if ($isUpdated) {
                $mailData = array(
                    'site_url' => $request->site_url . "?token=" . $password_reset_token,
                    'email' => $request->email,
                    "name" => $user->first_name . " " . $user->last_name
                );
                //Lets send email
                $sendVerificationEmail = new EmailController();
                $sendVerificationEmail->sendLinkIfForgotPassword($mailData);
                return $this->successResponse(true, 'Successfully sent password reset email');
            }

            return $this->errorResponse("Something went wrong! please try again", 422);
        }

        return $this->errorResponse("User not found! Please enter correct email address", 422);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Wrong username or password',
                'data' => NULL
            ], 200);
        }

        $user = User::whereEmail($request->email)->firstOrFail();

        //If user type is admin, he should be not able to login
        if ($user->user_type === 'admin') {
            return response()->json([
                'status' => 'fail',
                'message' => 'You are not allowed to login with this account.',
                'data' => NULL
            ], 200);
        }

        // If email not verified. do not let them sign in
        if (!$user->is_email_verified) {
            return response()->json([
                'status' => 'email-not-verified',
                'message' => 'Please verify your email to sign in.',
                'data' => NULL
            ], 200);
        }

        // if not active, do not allow user to login
        if ($user->status !== 'approve') {
            return response()->json([
                'status' => 'not-active',
                'message' => 'Your account is not active',
                'data' => NULL
            ], 200);
        }

        $token = $user->createToken('byparts-marketplace')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                "user" => $user,
                'token' => $token
            ]
        ], 200);
    }

    public function handleProviderCallback($provider, Request $request)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        //To get first name and last name
        switch ($provider) {
            case 'facebook' || 'google':
                $first_name = $request->input('firstName');
                $last_name = $request->input('lastName');
                $email = $request->input('email');
                $provider = $request->input('provider');
                $provider_id = $request->input('id');
                $avatar = $request->input('photoUrl');
                break;
            default:
                $first_name = '';
                $last_name = '';
                $email = '';
                $provider = '';
                $provider_id = '';
                $avatar = '';
        }
        // Performing some validation
        $dataForValidation = [
            "first_name" => $first_name,
            "email" => $email,
            "provider" => $provider,
            "provider_id" => $provider_id
        ];
        $validator = Validator::make($dataForValidation, [
            'email' => 'required|email',
            'provider' => 'required',
            'provider_id' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        // Check if user alredy has an account with this email address
        $isUserHaveAccounnt = User::where('email', $email)->where('provider', NULL)->first();
        if($isUserHaveAccounnt !== NULL) {
            return $this->errorResponse("Your account was created using email and password, please use login form", 403);
        }

        // If user signup with different provider, let them know
        $isUserWithOtherprovider = User::where('email', $email)->where('provider','!=', $provider)->first();
        if($isUserWithOtherprovider !== NULL) {
            $getProviderName = User::where('email', $request->get('email'))->value('provider');
            return $this->errorResponse("Your account was created using  different provider, please use ". $getProviderName . " to sign in", 403);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $email
            ],
            [
                'email_verified_at' => now(),
                'is_email_verified' => true,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'avatar' => $avatar,
                'status' => 'approve',
                'provider' => $provider,
                'provider_id' => $provider_id,
                'user_type' => 'user'
            ]
        );

        $token = $userCreated->createToken('byparts-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                "user" => $userCreated,
                'token' => $token
            ]
        ], 200);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'google'])) {
            return response()->json(['error' => 'Please login using facebook or google'], 422);
        }
    }

    public function verifyUserMobile(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'mobile_verification_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $isValid = User::where('id', $user->id)->where('mobile_verification_code', $request->mobile_verification_code)->first();

        if($isValid !== null) {

            User::where('id', $user->id)->update([
                'is_mobile_verified' => true,
                'phone_verified_at' => now(),
            ]);
            return $this->successResponse(true, 'Successfully verified user mobile');
        }

        return $this->errorResponse("Invalid code, please enter valid mobile verification code");
    }

    /**
     * TODO: Move username/password/sendId to environemnt variables
     */
    public function sendTacToUserMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $user_id = Auth::user()->id;
        $isMobileTaken = User::where('mobile_no', $request->mobile_no)
                            ->where('id', '!=', $user_id)->first();

        if($isMobileTaken !== null) {
            return $this->errorResponse("The number is already taken, please use different number");
        }

        $six_digit_random_number = random_int(100000, 999999);

        $user = User::findOrFail($user_id);
        $currentNumberInStore = $user->mobile_no;


        if($currentNumberInStore === $request->mobile_no && $user->is_mobile_verified === 1) {
            return $this->errorResponse('Your mobile is already verified');
        }

        $user->update([
            'mobile_verification_code' => $six_digit_random_number,
            'mobile_no' => $request->mobile_no,
            'is_mobile_verified' => false,
        ]);

        dispatch(new SendMobileTac($six_digit_random_number, $request->mobile_no));
        return $this->successResponse(true, "Sending SMS! Please make sure your mobile number is valid");
    }
}
