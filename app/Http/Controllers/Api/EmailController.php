<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Throwable;

class EmailController extends Controller
{
    protected $fromEmail = NULL;
    protected $apiKey = NULL;

    function __construct() {
        $this->fromEmail = config('bypart.mail_from_address');
        $this->apiKey = config('bypart.mail_passowrd');
    }

    /**
     * To send verification email when user signup
     */
    public function sendVerificationEmail($data)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->fromEmail, "Byparts Motor");
        $email->setSubject("Byparts motor - Email verification link");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables
        $email->addTo(
            $data['email'],
            $data['name'],
            [
                'link' => $data["site_url"],
                'User' => $data['name']
            ]
        );

        $email->setTemplateId("d-6dd6857df45940ff917901a370eb47e3");
        $sendgrid = new \SendGrid($this->apiKey);

        try {
            $sendgrid->send($email);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * To send email to user to verify their email address again.
     */
    public function resendVerificationEmail($data)
    {

    }

    /**
     * Send if user frogot password
     */
    public function sendLinkIfForgotPassword($data)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->fromEmail, "Byparts Motor");
        $email->setSubject("Byparts motor - Forgot  link");

        //We can send variables values on sendgrid templates using addTO
        // line = added as dynamic variables
        $email->addTo($data['email']);
        $email->addDynamicTemplateDatas([
            "link" => $data['site_url'],
            "User" => $data['name']
        ]);

        $email->setTemplateId("d-53bdb21349aa464fb624093a2fac0c6c");
        $sendgrid = new \SendGrid($this->apiKey);

        try {
            $sendgrid->send($email);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
