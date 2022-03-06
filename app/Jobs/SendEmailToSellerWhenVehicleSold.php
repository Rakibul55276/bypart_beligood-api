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
use App\Models\Listing\ListingAuctionBid;
use App\Helper\ListingRelated;

class SendEmailToSellerWhenVehicleSold implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;
    public $listing_id;
    public $sellAmount;
    public $buyer_name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $listing_id, $sellAmount, $buyer_name)
    {
        $this->user_id = $user_id;
        $this->listing_id = $listing_id;
        $this->sellAmount = $sellAmount;
        $this->buyer_name = $buyer_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sellerEmail = User::where('id', $this->user_id)->get(['email','first_name'])->toArray();
        $listingDetails = Listing::where('id', $this->listing_id)->get(['manufacture_year','car_make_name','model','variant'])->toArray();
        //Get image src from helper
        $image_src = ListingRelated::getImageForListing($this->listing_id);

        $api_key = config('bypart.mail_passowrd');
        $from_email =  config('bypart.mail_from_address');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("Sold Vehicle");

        $email->addTo($sellerEmail[0]['email']);

        $email->addDynamicTemplateDatas([
            "user" => $sellerEmail[0]['first_name'],
            "buyer" => $this->buyer_name,
            "sold_price" => $this->sellAmount,
            "listing_details" => $listingDetails[0]['manufacture_year'] .' '.$listingDetails[0]['car_make_name'] .' '.$listingDetails[0]['model'] .' '.$listingDetails[0]['variant'],
            "listing_url" => config('bypart.frontend_url').'pages/buy/cars/'.$this->listing_id,
            "src" => $image_src
        ]);

        $email->setTemplateId("d-bfadb2ae969d4a9f88eb17a2bf9abbc0");
        $sendgrid = new \SendGrid($api_key);

        $sendgrid->send($email);
    }
}
