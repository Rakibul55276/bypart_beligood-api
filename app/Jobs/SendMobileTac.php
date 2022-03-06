<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMobileTac implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $six_digit_random_number;
    public $mobile_no;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($six_digit_random_number, $mobile_no)
    {
        $this->six_digit_random_number = $six_digit_random_number;
        $this->mobile_no = $mobile_no;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sender_id = urlencode("66300");
        $username = urlencode("Beligood");
        $password = urlencode("P@ssw0rd");


        $message = "Welcome to Beligood, Your mobile verification code is ". $this->six_digit_random_number;
        $message = html_entity_decode($message, ENT_QUOTES, 'utf-8');
        $message = urlencode($message);

        $destination = $this->mobile_no;
        $destination = urlencode($destination);


        $fp = "https://www.isms.com.my/isms_send.php";
        $fp .= "?un=$username&pwd=$password&dstno=$destination&msg=$message&type=1&agreedterm=Yes&sendid=$sender_id";

        $this->exaByteSendSMS($fp);
    }

    protected function exaByteSendSMS($link) {
        $http = curl_init($link);
        curl_setopt($http, CURLOPT_RETURNTRANSFER, TRUE);
        $http_result = curl_exec($http);
        $http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);       curl_close($http);
        return $http_result;
    }
}
