<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService
{
    public function sendVerificationCode(string $phone, string $code) : void
    {
        $message = "iRestTor verification code: {$code}";

        $response = Http::withToken(config('services.sms.token'))
        ->acceptJson()
        ->withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->post(config('services.sms.url'), [
            'api_key' => config('services.sms.api_key'),
            'to' => $phone,
            'message' => $message,
        ]);
        
        if (! $response->successful()) {
            Log::error('SMS send failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new RuntimeException('Failed to send SMS verification code.');
        }

        Log::info('SMS sent successfully', [
            'phone' => $phone,
            'response' => $response->json(),
        ]);

    }
}
