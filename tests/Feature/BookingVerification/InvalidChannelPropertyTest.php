<?php

namespace Tests\Feature\BookingVerification;

use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvalidChannelPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 1: Invalid or absent channel is rejected
     * with no side effects. For any string value that is not sms or whatsapp (including a
     * missing delivery_channel field), a send request SHALL return HTTP 422 and SHALL NOT
     * create a BookingVerification record or dispatch any delivery job.
     * Validates: Requirements 2.2, 2.3, 2.4, 2.6, 10.1
     */
    public function test_invalid_or_absent_channel_is_rejected_with_no_side_effects(): void
    {
        Bus::fake();

        $business = $this->makeBusiness();
        $service = $this->makeService($business);

        // Note: leading/trailing whitespace is normalised away by Laravel's global
        // TrimStrings middleware before validation runs, so such values are excluded here.
        $edgeCases = ['', 'Sms', 'SMS', 'WhatsApp', 'WHATSAPP', 'telegram', '0', 'null', 'true', '["sms"]'];

        $cases = $edgeCases;
        while (count($cases) < 100) {
            $cases[] = Str::random(12);
        }

        foreach ($cases as $invalidChannel) {
            $payload = $this->sendPayload($service, ['delivery_channel' => $invalidChannel]);

            $response = $this->postJson(route('booking.verification.send', $business->slug), $payload);

            $response->assertStatus(422);
        }

        // Absent field entirely.
        $payload = $this->sendPayload($service);
        unset($payload['delivery_channel']);

        $this->postJson(route('booking.verification.send', $business->slug), $payload)
            ->assertStatus(422);

        $this->assertSame(0, BookingVerification::count());
        Bus::assertNothingDispatched();
    }
}
