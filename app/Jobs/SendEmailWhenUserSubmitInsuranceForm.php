<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailWhenUserSubmitInsuranceForm implements ShouldQueue
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
        $admin_email = "Admin@byparts.com";

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($from_email, "Beligood");
        $email->setSubject("Insurance form submitted");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables
        $email->addTo($admin_email);

        $email->addDynamicTemplateDatas([
            "name" => $this->data['name'],
            "mobile_no" => $this->data['mobile_no'],
            "email" => $this->data['email'],
            "born_in" => $this->data['born_in'],
            "driving_year" => $this->data['driving_year'],
            "no_of_claims" => $this->data['no_of_claims_in_past_three_years'],
            "ncd" => $this->data['ncd'],
            "car_make" => $this->data['car_make'],
            "car_model" => $this->data['car_model'],
            "gender" => $this->data['gender'],
            "marital_status" => $this->data['marital_status']
        ]);

        $email->setTemplateId("d-7832836f94894fd0b5df82b8d03d888e");
        $sendgrid = new \SendGrid($api_key);


        $sendgrid->send($email);
    }
}
