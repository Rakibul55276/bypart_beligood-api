<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Notification\Notifications;
use App\Events\NotificationEvent;

use DateTime;
use DateTimeZone;


class NotificationController extends ApiController
{
    public function getNotifications(Request $request)
    {
        $user_id = Auth::user()->id;
        $limit = $request->limit ? $request->limit : 10;
        $query = Notifications::where('user_id', $user_id)->orderBy('created_at', 'desc')->paginate($limit);

        return $this->successResponse($query);
    }

    public function saveNotification($notification_header = '', $notification_content = '', $notification_type = '', $url = '', $sender_id = '', $user_ids = array(), $listing_id = null)
    {

        $notification_array = array();
        foreach ($user_ids as $key => $value) {
            $notification_array[$key]['notification_header'] = $notification_header;
            $notification_array[$key]['notification_content'] = $notification_content;
            $notification_array[$key]['notification_type'] = $notification_type;
            $notification_array[$key]['url'] = $url;
            $notification_array[$key]['sender_id'] = $sender_id;
            $notification_array[$key]['user_id'] = $value;
            $notification_array[$key]['listing_id'] = $listing_id;
            $notification_array[$key]['created_at'] = now();

            //Lets notify user 
            broadcast(new NotificationEvent($value));
        }

        Notifications::insert($notification_array);
    }

    public function markNotificationAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        Notifications::where('id', $request->notification_id)->update([
            'is_seen' => true
        ]);

        //Lets notify user 
        broadcast(new NotificationEvent(Auth::user()->id));
    }

    public function markAllAsRead(Request $request)
    {
        $user_id = Auth::user()->id;
        $notiicationIds = Notifications::where('user_id', $user_id)->where('is_seen', 0)->pluck('id');
        foreach ($notiicationIds as $notiicationId) {
            Notifications::where('id', $notiicationId)->update([
                'is_seen' => true
            ]);
        }
        //Lets notify user 
        broadcast(new NotificationEvent($user_id));
    }
}
