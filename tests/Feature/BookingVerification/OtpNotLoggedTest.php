<?php

namespace Tests\Feature\BookingVerification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class OtpNotLoggedTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);

        config([
            'services.sms.url' => 'https://sms.example.test/send',
            'services.twilio.sid' => 'AC_TEST_SID',
            'services.twilio.token' => 'test-token',
            'services.twilio.whatsapp_from' => 'whatsapp:+14155238886',
            'services.twilio.otp_template_sid' => 'HX_TEST_TEMPLATE',
        ]);
    }

    /**
     * Req 12.14 (complements Property 7): running the full send() endpoint on either the
     * SMS or WhatsApp path, on both the success and failure branches, SHALL never produce a
     * log entry containing a 6-digit OTP-shaped sequence.
     */
    public function test_otp_code_never_appears_in_logs_across_both_delivery_paths(): void
    {
        $capturedLines = [];
        Log::listen(function ($event) use (&$capturedLines) {
            $capturedLines[] = $event->message . ' ' . json_encode($event->context);
        });

        $business = $this->makeBusiness();
        $premiumBusiness = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $premiumService = $this->makeService($premiumBusiness);

        $scenarios = [
            'sms-success' => ['sms.example.test/*', ['ok' => true], 200, $business, $service],
            'sms-failure' => ['sms.example.test/*', ['ok' => false], 500, $business, $service],
            'whatsapp-success' => ['api.twilio.com/*', ['sid' => 'SM_TEST'], 201, $premiumBusiness, $premiumService],
            'whatsapp-failure' => ['api.twilio.com/*', ['code' => 63016], 400, $premiumBusiness, $premiumService],
        ];

        foreach ($scenarios as $key => [$urlPattern, $body, $status, $biz, $svc]) {
            [$channel] = explode('-', $key);

            for ($i = 0; $i < 25; $i++) {
                Http::fake([$urlPattern => Http::response($body, $status)]);

                $phone = $this->randomPhone();
                $capturedLines = [];

                $this->postJson(route('booking.verification.send', $biz->slug), $this->sendPayload($svc, [
                    'customer_phone' => $phone,
                    'delivery_channel' => $channel,
                ]));

                foreach ($capturedLines as $line) {
                    $this->assertDoesNotMatchRegularExpression(
                        '/\b\d{6}\b/',
                        $line,
                        "OTP-shaped 6-digit sequence leaked into a log entry during {$key}."
                    );
                }
            }
        }
    }
}
