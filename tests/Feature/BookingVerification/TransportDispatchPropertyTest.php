<?php

namespace Tests\Feature\BookingVerification;

use App\Jobs\SendOtpSmsJob;
use App\Jobs\SendOtpWhatsappJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class TransportDispatchPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 4: Correct transport is dispatched for the
     * chosen channel. For any accepted send request, the dispatched delivery job SHALL
     * correspond to the chosen channel (sms -> SendOtpSmsJob, whatsapp -> SendOtpWhatsappJob),
     * carrying the normalised phone and the generated code; and for any phone, the WhatsApp
     * recipient SHALL be whatsapp: prefixed to the normalised +972 phone with the OTP passed
     * as a Content template variable (covered separately in WhatsappServiceOtpTest).
     * Validates: Requirements 4.1, 4.2, 5.1, 5.2, 5.6
     */
    public function test_correct_transport_dispatched_for_chosen_channel(): void
    {
        Bus::fake();

        $business = $this->makeBusiness();
        $premiumBusiness = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $premiumService = $this->makeService($premiumBusiness);

        for ($i = 0; $i < 50; $i++) {
            $phone = $this->randomPhone();
            $normalizedPhone = $this->normalizedPhone($phone);

            $payload = $this->sendPayload($service, [
                'customer_phone' => $phone,
                'delivery_channel' => 'sms',
            ]);

            $this->postJson(route('booking.verification.send', $business->slug), $payload)
                ->assertStatus(200);

            $job = Bus::dispatchedSync(SendOtpSmsJob::class)->last();

            $this->assertNotNull($job);
            $this->assertSame($normalizedPhone, $job->phone);
            $this->assertMatchesRegularExpression('/^\d{6}$/', $job->code);
        }

        for ($i = 0; $i < 50; $i++) {
            $phone = $this->randomPhone();
            $normalizedPhone = $this->normalizedPhone($phone);

            $payload = $this->sendPayload($premiumService, [
                'customer_phone' => $phone,
                'delivery_channel' => 'whatsapp',
            ]);

            $this->postJson(route('booking.verification.send', $premiumBusiness->slug), $payload)
                ->assertStatus(200);

            $job = Bus::dispatchedSync(SendOtpWhatsappJob::class)->last();

            $this->assertNotNull($job);
            $this->assertSame($normalizedPhone, $job->phone);
            $this->assertMatchesRegularExpression('/^\d{6}$/', $job->code);
        }
    }
}
