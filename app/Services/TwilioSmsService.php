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
        $sid = env('TWILIO_SID', 'ACed47b49e578c60d753ff3ed98b116c76');
        $token = env('TWILIO_AUTH_TOKEN', '2de15d24afb0138012d7e5629d541a33');
        $this->fromNumber = env('TWILIO_PHONE_NUMBER', '+16167396953');

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
