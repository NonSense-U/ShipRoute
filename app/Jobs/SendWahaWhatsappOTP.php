<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWahaWhatsappOTP implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $phoneNumber, public string $otp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processed_phone_number = '963' . preg_replace('/^0/', '', $this->phoneNumber);

        
        $message = "Hi! 👋\n\n"
            . "Your shipment is almost there.\n\n"
            . "Your delivery verification code is: *{$this->otp}*\n\n"
            . "Please share this code with the delivery agent when you receive your package.\n\n"
            . "⏳ This code will expire in 10 minutes.\n\n"
            . "Thank you for choosing us! 📦";


        $result = Http::withHeaders([
            'X-API-Key' => config('services.waha.api_key'),
            'Accept' => 'application/json',
        ])->post(config('services.waha.api_url') . 'sendText', [
            "chatId" => $processed_phone_number . "@c.us",  
            "id" => null,
            "reply_to" => null,
            "text" => $message,
            "linkPreview" => true,
            "linkPreviewHighQuality" => false,
            "session" => "default"
        ]);

        if ($result->failed()) {
            Log::error('Failed to send WhatsApp OTP', [
                'phone_number' => $this->phoneNumber,
                'otp' => $this->otp,
                'response' => $result->body(),
            ]);
        }
    }
}
