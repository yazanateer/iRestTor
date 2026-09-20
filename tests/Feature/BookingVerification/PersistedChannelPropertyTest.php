<?php

namespace Tests\Feature\BookingVerification;

use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class PersistedChannelPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 2: Persisted channel equals the submitted
     * channel. For any accepted send request, the persisted BookingVerification.delivery_channel
     * SHALL equal the submitted value and SHALL be a member of {sms, whatsapp}; and for any
     * resend for the same business+phone, the prior record SHALL be deleted and the new record
     * SHALL store the same channel with a fresh expires_at and code_hash.
     * Validates: Requirements 3.5, 7.1, 7.2, 7.3, 7.4, 9.8
     */
    public function test_persisted_channel_equals_submitted_and_resend_resets_record(): void
    {
        Bus::fake();

        $business = $this->makeBusiness();
        $premiumBusiness = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $premiumService = $this->makeService($premiumBusiness);

        $scenarios = [
            'sms' => [$business, $service],
            'whatsapp' => [$premiumBusiness, $premiumService],
        ];

        for ($i = 0; $i < 50; $i++) {
            foreach ($scenarios as $channel => [$biz, $svc]) {
                $phone = $this->randomPhone();
                $normalizedPhone = $this->normalizedPhone($phone);

                $payload = $this->sendPayload($svc, [
                    'customer_phone' => $phone,
                    'delivery_channel' => $channel,
                ]);

                $this->postJson(route('booking.verification.send', $biz->slug), $payload)
                    ->assertStatus(200)
                    ->assertJson(['success' => true]);

                $first = BookingVerification::query()
                    ->where('business_id', $biz->id)
                    ->where('customer_phone', $normalizedPhone)
                    ->first();

                $this->assertNotNull($first);
                $this->assertSame($channel, $first->delivery_channel);
                $this->assertContains($first->delivery_channel, ['sms', 'whatsapp']);

                // Resend: same business + phone.
                $this->postJson(route('booking.verification.send', $biz->slug), $payload)
                    ->assertStatus(200);

                $matching = BookingVerification::query()
                    ->where('business_id', $biz->id)
                    ->where('customer_phone', $normalizedPhone)
                    ->get();

                $this->assertCount(1, $matching, 'Resend must delete the prior record and leave exactly one.');

                $second = $matching->first();
                $this->assertNotSame($first->id, $second->id);
                $this->assertSame($channel, $second->delivery_channel);
                $this->assertNotSame($first->code_hash, $second->code_hash);
                $this->assertTrue($second->expires_at->greaterThanOrEqualTo($first->expires_at));
            }
        }
    }
}
