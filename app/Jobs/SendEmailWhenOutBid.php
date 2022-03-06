<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing\Listing;
use App\Models\Listing\ListingAuctionBidAuto;
use App\Models\Listing\ListingAuctionBid;
use App\Models\User;

class SendEmailWhenOutBid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $name;
    public $listing_link;
    public $listing_id;
    public $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $name, $listing_link, $listing_id, $user_id)
    {
        $this->email = $email;
        $this->name = $name;
        $this->listing_link = $listing_link;
        $this->listing_id = $listing_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ad_title = Listing::where('id', $this->listing_id)->value('ad_title');
        $current_bid_amount = ListingAuctionBid::where('listing_id', $this->listing_id)->max('bid_amount');

        $user_id = User::where('email', $this->email)->value('id');
        $usrMaxBidAmount = ListingAuctionBid::where('user_id', $user_id)->where('listing_id', $this->listing_id)->max('bid_amount');
        $user_bid_amount = $usrMaxBidAmount === null ? 'You do not have auto bid set' : $usrMaxBidAmount;

        $api_key = config('bypart.mail_passowrd');
        $from_email =  config('bypart.mail_from_address');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("You are Outbid");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables

        $email->addTo($this->email);

        $email->addDynamicTemplateDatas([
            "user" => $this->name,
            "listing_link" => $this->listing_link,
            "ad_title" => $ad_title,
            "current_bid" => $current_bid_amount,
            "user_max_bid" => $user_bid_amount,
        ]);

        $email->setTemplateId("d-92a64b44f8ed49f1895c7ae66abe9ec4");
        $sendgrid = new \SendGrid($api_key);
        /**
         * To check if user not high bidder, then only execute email
         */
        $highest_bidder = ListingAuctionBid::where('listing_id', $this->listing_id)->orderBy('bid_amount', 'desc')->first();
        $highest_bidder_id = $highest_bidder !== null ? $highest_bidder->user_id : 0;

        if($highest_bidder_id !== $this->user_id) {
            $sendgrid->send($email);
        }
    }
}
