<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Notification\NotificationController;
use Illuminate\Http\Request;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingQuestionAnswer;
use App\Models\Listing\ListingQuestionAnswerReply;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiController;
use App\Jobs\SendEmailToSellerWhenQuestionAsk;
use App\Jobs\SendEmailToUserWhenQuestionReply;

use App\Models\User;

class CarListingQAController extends ApiController
{
    public function saveQuestion(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }
        if ($request->question !== null) {
            $is_question_exist = ListingQuestionAnswer::where('question', $request->question)
                ->where('listing_id', $request->listing_id)->first();

            if (!is_null($is_question_exist)) {
                return $this->errorResponse("The question exist! please reply instead");
            }
        }

        $question_images = $request->question_images ? $request->question_images : "";
        $create_question = ListingQuestionAnswer::create([
            "question" => $request->question,
            "listing_id" => $request->listing_id,
            "question_images" => $question_images,
            "question_view_count" => 0,
            "question_asked_by_user_id" => $user->id
        ]);

        $ownerid = Listing::where('id', $request->listing_id)->value('user_id');


        if($ownerid !== $user->id){
            //Send email notification to listing owner
            $ownerDetails = User::select('first_name', 'last_name', 'email')->where('id', $ownerid)->first();
            $senderDetails = User::select('first_name', 'last_name', 'email')->where('id', $user->id)->first();
            $reciver_name = $ownerDetails->first_name . ' ' . $ownerDetails->last_name;
            $sender_name = $senderDetails->first_name . ' ' . $senderDetails->last_name;

            dispatch(new SendEmailToSellerWhenQuestionAsk($request->listing_id, $ownerDetails->email, $reciver_name, $sender_name, $request->question));
            // End send email

            /**
             * Call Notification Global Saving API Start
             */
            $notification_header = 'New Question Added';
            $notification_content = $user->first_name . " " . $user->last_name . " Asked a Question on your listing. Please click to View Details.";
            $notification_type = 'question';
            $ref_id = $request->listing_id;
            $sender_id = $user->id;
            $user_ids = Listing::where('id', $request->listing_id)->value('user_id');
            $user_ids = array($user_ids);
            $url = config('bypart.frontend_url') . 'pages/buy/cars/' . $request->listing_id;

            app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $user_ids, $request->listing_id);
        }
            /**
         * Call Notification Global Saving API End
         */

        if ($create_question) {
            return $this->successResponse($create_question);
        }

        return $this->errorResponse("Whoops! fail to submit question");
    }

    public function saveReply(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'listing_question_answer_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $is_question_answer_exist = ListingQuestionAnswerReply::where('reply', $request->reply)
            ->where('listing_question_answer_id', $request->listing_question_answer_id)->first();

        if (!is_null($is_question_answer_exist)) {
            return $this->errorResponse("The answer exist! please check");
        }

        $reply_images = $request->reply_images ? $request->reply_images : "";
        $create_reply = ListingQuestionAnswerReply::create([
            "reply" => $request->reply,
            "reply_images" => $reply_images,
            "listing_question_answer_id" => $request->listing_question_answer_id,
            "question_reply_by_user_id" => $user->id
        ]);

        $listing_id = ListingQuestionAnswer::where('id', $request->listing_question_answer_id)->value('listing_id');
        //Send email notification to who ask question
        $ownerid = ListingQuestionAnswer::where('id', $request->listing_question_answer_id)->value('question_asked_by_user_id');

        if($ownerid !== $user->id){
            $ownerDetails = User::select('first_name', 'last_name', 'email')->where('id', $ownerid)->first();

            $senderDetails = User::select('first_name', 'last_name', 'email')->where('id', $user->id)->first();

            $reciver_name = $ownerDetails->first_name . ' ' . $ownerDetails->last_name;
            $sender_name = $senderDetails->first_name . ' ' . $senderDetails->last_name;

            dispatch(new SendEmailToUserWhenQuestionReply($listing_id, $ownerDetails->email, $reciver_name, $sender_name, $request->reply));
            //End dispatch email

            /**
             * Call Notification Global Saving API Start
             */
            $notification_header = 'New Reply Added';
            $notification_content = $user->first_name . " " . $user->last_name . " Replied to a Question. Please click to View Details.";
            $notification_type = 'question_reply';

            $sender_id = $user->id;
            //Since paramet accept array only
            $receiver_ids = ListingQuestionAnswer::where('id', $request->listing_question_answer_id)->pluck('question_asked_by_user_id');
            $url = config('bypart.frontend_url') . 'pages/buy/cars/' . $listing_id;
            app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $receiver_ids, $listing_id);
            /**
             * Call Notification Global Saving API End
             */
        }

        if ($create_reply) {
            return $this->successResponse($create_reply);
        }

        return $this->errorResponse("Whoops! fail to submit reply");
    }

    public function getQuestionForSingleListing($list_id)
    {
        $qa = ListingQuestionAnswer::with('listing_question_answer_reply.user:id,first_name,last_name,avatar', 'user:id,first_name,last_name,avatar')->where('listing_id', $list_id)->get();
        return $this->successResponse($qa);
    }

    public function deleteQuestion(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'question_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $question = ListingQuestionAnswer::where('question_asked_by_user_id', $user->id)->find($request->question_id);

        if ($question === null) {
            return $this->errorResponse('You are not allowd to delete this question', 422);
        }

        //Deleting all reply and then delete question
        $question->listing_question_answer_reply()->delete();
        $question->delete();

        return $this->successResponse(true, 'Question deleted successfully');
    }

    public function deleteQuestionReply(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'question_reply_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $questionReply = ListingQuestionAnswerReply::where('question_reply_by_user_id', $user->id)->find($request->question_reply_id);

        if ($questionReply === null) {
            return $this->errorResponse('You are not allowd to delete this reply', 422);
        }

        $questionReply->delete();
        return $this->successResponse(true, 'Reply deleted successfully');
    }
}
