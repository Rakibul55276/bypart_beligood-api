<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailWhenUserSubmitCarLoanForm implements ShouldQueue
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
        $email->setSubject("Car Loan request form submitted");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables
        $email->addTo($admin_email);

        $email->addDynamicTemplateDatas([
            "bank_name" => $this->data['bank_name'],
            "car_price" => $this->data['car_price'],
            "down_payment" => $this->data['down_payment'],
            "loan_amount" => $this->data['loan_amount'],
            "bank_rate" => $this->data['bank_rate'],
            "payment_term" => $this->data['payment_term'],
            "monthly_repayment" => $this->data['monthly_repayment'],
            "car_condition" => $this->data['car_condition'],
            "car_origin" => $this->data['car_origin'],
            "name" => $this->data['name'],
            "mobile_no" => $this->data['mobile_no'],
            "email" => $this->data['email'],
            "car_make" => $this->data['car_make'],
            "year" => $this->data['year']
        ]);

        $email->setTemplateId("d-df6c1c3ca4774d69a5d246ee59424e9b");
        $sendgrid = new \SendGrid($api_key);


        $sendgrid->send($email);
    }
}
