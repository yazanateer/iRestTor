<?php

namespace Tests\Feature\BookingVerification;

use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class PremiumGatePropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 5: Premium gate blocks WhatsApp for
     * non-premium businesses with no side effects. For any non-premium business, a send
     * request with delivery_channel = whatsapp SHALL return HTTP 422 and SHALL NOT create
     * a BookingVerification or dispatch a WhatsApp job.
     * Validates: Requirements 2.5, 6.1
     */
    public function test_premium_gate_blocks_whatsapp_for_non_premium_with_no_side_effects(): void
    {
        Bus::fake();

        $business = $this->makeBusiness();
        $service = $this->makeService($business);

        for ($i = 0; $i < 100; $i++) {
            $phone = $this->randomPhone();

            $payload = $this->sendPayload($service, [
                'customer_phone' => $phone,
                'delivery_channel' => 'whatsapp',
            ]);

            $this->postJson(route('booking.verification.send', $business->slug), $payload)
                ->assertStatus(422);
        }

        $this->assertSame(0, BookingVerification::count());
        Bus::assertNothingDispatched();
    }
}
