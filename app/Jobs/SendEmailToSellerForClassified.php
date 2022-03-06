<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing\Listing;

class SendEmailToSellerForClassified implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api_key = config('bypart.mail_passowrd');
        $from_email =  config('bypart.mail_from_address');

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("Beligood classified user wants to contact with you.");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables
        $email->addTo($this->data['seller_email']);

        // if buyer email. send him copy as well
        if($this->data['email_me_copy']) {
            $email->addTo($this->data['email']);
        }

        $email->addDynamicTemplateDatas([
            "sender_name" => $this->data['name'],
            "sender_phone" => $this->data['mobile_no'],
            "sender_message" => $this->data['message'],
            "listing_title" => $this->data['year'].' '.$this->data['make'].' '.$this->data['model'].' '.$this->data['variant'],
            "image" => $this->data['image_src'],
            "year" => $this->data['year'],
            "make" => $this->data['make'],
            "model" => $this->data['model'],
            "variant" => $this->data['variant'],
            "asking_price" => $this->data['asking_price']
        ]);

        $email->setTemplateId("d-0a1ad6d2d60344a389dd30281b810efe");
        $sendgrid = new \SendGrid($api_key);


        $sendgrid->send($email);
    }
}
