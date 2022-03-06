<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailPurchaseConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $buyer_email;
    protected $buyer_name;
    protected $vehicle_model;
    protected $purchase_time;
    protected $reference_id;
    protected $purchase_amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($buyer_email, $buyer_name, $vehicle_model, $purchase_time, $reference_id, $purchase_amount)
    {
        $this->buyer_email = $buyer_email;
        $this->buyer_name = $buyer_name;
        $this->vehicle_model = $vehicle_model;
        $this->purchase_time = $purchase_time;
        $this->reference_id = $reference_id;
        $this->purchase_amount = $purchase_amount;
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
        $email->setSubject("Purchase Confirmation");

        $email->addTo($this->buyer_email);

        $email->addDynamicTemplateDatas([
            "name" => $this->buyer_name,
            "vehicle_model" => $this->vehicle_model,
            "purchase_time" => $this->purchase_time,
            "reference_id" => $this->reference_id,
            "purchase_amount" => $this->purchase_amount,
        ]);

        $email->setTemplateId("d-ace331c44eed475a9ccb8f8844286a3f");
        $sendgrid = new \SendGrid($api_key);

        $sendgrid->send($email);
    }
}
