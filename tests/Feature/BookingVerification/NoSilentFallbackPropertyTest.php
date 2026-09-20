<?php

namespace Tests\Feature\BookingVerification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NoSilentFallbackPropertyTest extends TestCase
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
     * Feature: otp-delivery-channel, Property 6: No silent channel fallback. For any
     * selected channel whose delivery fails, the delivery job/service of the other channel
     * SHALL never be dispatched or invoked as a result of that failure.
     * Validates: Requirement 8.3
     */
    public function test_whatsapp_failure_never_falls_back_to_sms(): void
    {
        Http::fake([
            'api.twilio.com/*' => Http::response(['code' => 63016, 'message' => 'Failed'], 400),
            'sms.example.test/*' => Http::response(['ok' => true], 200),
        ]);

        $business = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();

        $payload = $this->sendPayload($service, [
            'customer_phone' => $phone,
            'delivery_channel' => 'whatsapp',
        ]);

        $this->postJson(route('booking.verification.send', $business->slug), $payload)
            ->assertStatus(500);

        Http::assertSentCount(1);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'sms.example.test'));
    }

    public function test_sms_failure_never_falls_back_to_whatsapp(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::response(['ok' => false], 500),
            'api.twilio.com/*' => Http::response(['sid' => 'SM_TEST'], 201),
        ]);

        $business = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();

        $payload = $this->sendPayload($service, [
            'customer_phone' => $phone,
            'delivery_channel' => 'sms',
        ]);

        $this->postJson(route('booking.verification.send', $business->slug), $payload)
            ->assertStatus(500);

        Http::assertSentCount(1);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'api.twilio.com'));
    }
}
