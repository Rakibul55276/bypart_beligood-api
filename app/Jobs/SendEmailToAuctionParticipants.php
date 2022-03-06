<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Listing\Listing;


class SendEmailToAuctionParticipants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $listing_id;
    protected $bid_remark;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $listing_id, $bid_remark)
    {
        $this->user_id = $user_id;
        $this->listing_id = $listing_id;
        $this->bid_remark = $bid_remark;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userEmail = User::where('id', $this->user_id)->get(['email','first_name'])->toArray();
        $listingDetails = Listing::where('id', $this->listing_id)->get(['manufacture_year','car_make_name','model','variant'])->toArray();

        $api_key = config('bypart.mail_passowrd');
        $from_email =  config('bypart.mail_from_address');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("Congratulations");

        $email->addTo($userEmail[0]['email']);

        $email->addDynamicTemplateDatas([
            "user" => $userEmail[0]['first_name'],
            "listing_details" => $listingDetails[0]['manufacture_year'] .' '.$listingDetails[0]['car_make_name'] .' '.$listingDetails[0]['model'] .' '.$listingDetails[0]['variant'],
            "listing_url" => config('bypart.frontend_url').'pages/buy/cars/'.$this->listing_id,
            "remark" => $this->bid_remark
        ]);

        $email->setTemplateId("d-8ec5036ae39e46d4a0dcccc0e077b3ba");
        $sendgrid = new \SendGrid($api_key);


        $sendgrid->send($email);

    }
}
