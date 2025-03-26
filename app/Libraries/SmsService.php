<?php

namespace App\Libraries;

use Twilio\Rest\Client;

class SmsService
{
    protected $client;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->client = new Client($sid, $token);
    }

    public function sendSms($to, $message)
    {
        $this->client->messages->create(
            $to,
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message,
            ]
        );
    }

    public function addCallerId($phoneNumber, $friendlyName)
    {
        $validation_request = $this->client->validationRequests->create("+919746530365",["friendlyName" => "Prasad"]);

        return $validation_request;
    }

    public function listCallerIds()
    {
        return $this->client->outgoingCallerIds->read();
    }

    public function deleteCallerId($callerIdSid)
    {
        return $this->client->outgoingCallerIds($callerIdSid)->delete();
    }

}