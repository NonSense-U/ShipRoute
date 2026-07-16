<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappOTP implements ShouldQueue
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
        $result = Http::withHeaders([
            'Authorization' => 'Bearer your-token',
            'X-API-Key' => config('services.whatsapp.api_key'),
            'Accept' => 'application/json',
        ])->post(config('services.whatsapp.api_url') . 'sendText', [
            "chatId" => $processed_phone_number . "@c.us",
            "id" => null,
            "reply_to" => null,
            "text" => (string) $this->otp,
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
