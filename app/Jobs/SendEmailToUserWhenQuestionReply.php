<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing\Listing;

class SendEmailToUserWhenQuestionReply implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $listing_id;
    protected $receiver_email;
    protected $reciver_name;
    protected $sender_name;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listing_id, $receiver_email,$reciver_name, $sender_name, $message)
    {
        $this->listing_id = $listing_id;
        $this->receiver_email = $receiver_email;
        $this->reciver_name = $reciver_name;
        $this->sender_name = $sender_name;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $listingDetails = Listing::where('id', $this->listing_id)->get(['manufacture_year','car_make_name','model','variant'])->toArray();

        $api_key = config('bypart.mail_passowrd');
        $from_email =  config('bypart.mail_from_address');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("New Message Reply Received.");

        $email->addTo($this->receiver_email);

        $email->addDynamicTemplateDatas([
            "user" => $this->reciver_name,
            "sender_name" => $this->sender_name,
            "listing_details" => $listingDetails[0]['manufacture_year'] .' '.$listingDetails[0]['car_make_name'] .' '.$listingDetails[0]['model'] .' '.$listingDetails[0]['variant'],
            "message" => $this->message,
            "url" => config('bypart.frontend_url').'pages/buy/cars/'.$this->listing_id
        ]);

        $email->setTemplateId("d-00c2f39bed5c4f23bfe9d958ada3a76c");
        $sendgrid = new \SendGrid($api_key);

        $sendgrid->send($email);
    }
}
