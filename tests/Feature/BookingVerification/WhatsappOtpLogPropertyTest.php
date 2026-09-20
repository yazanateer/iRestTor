<?php

namespace Tests\Feature\BookingVerification;

use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Tests\TestCase;

class WhatsappOtpLogPropertyTest extends TestCase
{
    /**
     * Feature: otp-delivery-channel, Property 7: For any generated OTP code, running either
     * the SMS or WhatsApp delivery path SHALL produce log output that does not contain the
     * plaintext code. This covers the WhatsApp path (Requirements 5.8, 10.2).
     */
    public function test_whatsapp_otp_code_never_appears_in_logs(): void
    {
        config([
            'services.twilio.sid' => 'AC_TEST_SID',
            'services.twilio.token' => 'test-token',
            'services.twilio.whatsapp_from' => 'whatsapp:+14155238886',
            'services.twilio.otp_template_sid' => 'HX_TEST_TEMPLATE',
        ]);

        $service = app(WhatsappService::class);

        $capturedLines = [];
        Log::listen(function ($event) use (&$capturedLines) {
            $capturedLines[] = $event->message . ' ' . json_encode($event->context);
        });

        for ($i = 0; $i < 100; $i++) {
            $code = (string) random_int(100000, 999999);
            $phone = '+9725' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            $shouldFail = $i % 2 === 0;

            Http::fake([
                'api.twilio.com/*' => $shouldFail
                    ? Http::response(['code' => 63016, 'message' => 'Failed'], 400)
                    : Http::response(['sid' => 'SM_TEST'], 201),
            ]);

            $capturedLines = [];

            try {
                $service->sendOtpCode($phone, $code);
            } catch (RuntimeException $e) {
                // Expected on the failure branch; assertions below still apply.
            }

            foreach ($capturedLines as $line) {
                $this->assertStringNotContainsString($code, $line, 'OTP plaintext leaked into a log entry.');
            }
        }
    }
}
