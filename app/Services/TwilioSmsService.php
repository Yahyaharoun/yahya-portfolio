<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioSmsService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->fromNumber = env('TWILIO_PHONE_NUMBER');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send an OTP SMS via Twilio.
     */
    public function sendOtp($to, $code)
    {
        if (!$this->client) {
            Log::error("Twilio credentials missing. SMS not sent to {$to}. Code: {$code}");
            return false;
        }

        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => $this->fromNumber,
                    'body' => "Votre code de sécurité Portfolio est : {$code}. Ce code expire dans 10 minutes."
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error("Twilio Error sending SMS to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
