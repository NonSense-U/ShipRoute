<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendUltraMessageWhatsappOTP implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $phoneNumber, public string $otp, public string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processedPhoneNumber = '+963' . preg_replace('/^0/', '', $this->phoneNumber);

        $result = Http::asForm()->post(
            config('services.ultramsg.api_url') . '/messages/chat',
            [
                'token' => config('services.ultramsg.token'),
                'to'    => $processedPhoneNumber,
                'body'  => $this->message,
            ]
        );

        if ($result->failed()) {
            Log::error('Failed to send WhatsApp OTP via UltraMsg', [
                'phone_number' => $this->phoneNumber,
                'otp'          => $this->otp,
                'status'       => $result->status(),
                'response'     => $result->body(),
            ]);
        }
    }
}
