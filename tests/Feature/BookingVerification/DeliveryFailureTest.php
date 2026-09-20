<?php

namespace Tests\Feature\BookingVerification;

use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeliveryFailureTest extends TestCase
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
     * Req 12.9: SMS delivery failure -> 500, translated message, no blocking record left behind.
     */
    public function test_sms_delivery_failure_returns_500_and_leaves_no_blocking_record(): void
    {
        Http::fake([
            'sms.example.test/*' => Http::sequence()
                ->push(['ok' => false], 500)
                ->push(['ok' => true], 200),
        ]);

        $business = $this->makeBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();

        $payload = $this->sendPayload($service, [
            'customer_phone' => $phone,
            'delivery_channel' => 'sms',
        ]);

        $response = $this->postJson(route('booking.verification.send', $business->slug), $payload);

        $response->assertStatus(500);
        $this->assertSame('booking.otpDeliveryFailed', $response->json('message'));

        $this->assertSame(
            0,
            BookingVerification::query()
                ->where('business_id', $business->id)
                ->where('customer_phone', $this->normalizedPhone($phone))
                ->count()
        );

        // A subsequent retry is not blocked by a leftover record.
        $this->postJson(route('booking.verification.send', $business->slug), $payload)
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Req 12.10: WhatsApp delivery failure -> 500, translated message, no SMS fallback,
     * no blocking record left behind.
     */
    public function test_whatsapp_delivery_failure_returns_500_and_leaves_no_blocking_record(): void
    {
        Http::fake([
            'api.twilio.com/*' => Http::response(['code' => 30001, 'message' => 'Queue overflow'], 500),
        ]);

        $business = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();

        $payload = $this->sendPayload($service, [
            'customer_phone' => $phone,
            'delivery_channel' => 'whatsapp',
        ]);

        $response = $this->postJson(route('booking.verification.send', $business->slug), $payload);

        $response->assertStatus(500);
        $this->assertSame('booking.otpDeliveryFailed', $response->json('message'));

        $this->assertSame(
            0,
            BookingVerification::query()
                ->where('business_id', $business->id)
                ->where('customer_phone', $this->normalizedPhone($phone))
                ->count()
        );

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'sms.example.test'));
    }

    /**
     * Req 8.4: the specific "number not on WhatsApp" Twilio error code SHALL surface a
     * distinct message suggesting SMS instead.
     */
    public function test_whatsapp_unregistered_number_returns_distinct_message(): void
    {
        Http::fake([
            'api.twilio.com/*' => Http::response(['code' => 63016, 'message' => 'not on whatsapp'], 400),
        ]);

        $business = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();

        $payload = $this->sendPayload($service, [
            'customer_phone' => $phone,
            'delivery_channel' => 'whatsapp',
        ]);

        $response = $this->postJson(route('booking.verification.send', $business->slug), $payload);

        $response->assertStatus(500);
        $this->assertSame('booking.whatsappNumberNotFound', $response->json('message'));
    }
}
