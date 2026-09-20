<?php

namespace Tests\Feature\BookingVerification;

use App\Jobs\SendOtpSmsJob;
use App\Jobs\SendOtpWhatsappJob;
use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SharedOtpInvariantsPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 8: Shared OTP invariants hold regardless of
     * channel. For any accepted send request on either channel, the generated code SHALL be
     * a 6-digit integer in [100000, 999999], code_hash SHALL be a bcrypt hash that verifies
     * against that code, expires_at SHALL equal creation time plus 5 minutes, attempts SHALL
     * start at 0, and lockout SHALL occur at 5 attempts.
     * Validates: Requirements 9.1, 9.2, 9.3, 9.4
     */
    public function test_shared_otp_invariants_hold_regardless_of_channel(): void
    {
        Bus::fake();

        $business = $this->makeBusiness();
        $premiumBusiness = $this->makePremiumBusiness();
        $service = $this->makeService($business);
        $premiumService = $this->makeService($premiumBusiness);

        $scenarios = [
            'sms' => [$business, $service, SendOtpSmsJob::class],
            'whatsapp' => [$premiumBusiness, $premiumService, SendOtpWhatsappJob::class],
        ];

        for ($i = 0; $i < 50; $i++) {
            foreach ($scenarios as $channel => [$biz, $svc, $jobClass]) {
                $phone = $this->randomPhone();
                $normalizedPhone = $this->normalizedPhone($phone);
                $before = now();

                $payload = $this->sendPayload($svc, [
                    'customer_phone' => $phone,
                    'delivery_channel' => $channel,
                ]);

                $this->postJson(route('booking.verification.send', $biz->slug), $payload)
                    ->assertStatus(200);

                $verification = BookingVerification::query()
                    ->where('business_id', $biz->id)
                    ->where('customer_phone', $normalizedPhone)
                    ->first();

                $job = Bus::dispatchedSync($jobClass)->last();

                $this->assertNotNull($verification);
                $this->assertNotNull($job);

                $code = (int) $job->code;
                $this->assertGreaterThanOrEqual(100000, $code);
                $this->assertLessThanOrEqual(999999, $code);
                $this->assertTrue(Hash::check($job->code, $verification->code_hash));
                $this->assertSame(0, $verification->attempts);
                $this->assertTrue(
                    $verification->expires_at->betweenIncluded(
                        $before->copy()->addMinutes(5)->subSeconds(2),
                        $before->copy()->addMinutes(5)->addSeconds(2)
                    )
                );
            }
        }
    }

    /**
     * Complements Property 8: lockout at the 5th failed attempt is identical for both channels.
     */
    public function test_lockout_at_five_attempts_holds_for_both_channels(): void
    {
        foreach (['sms', 'whatsapp'] as $channel) {
            $business = $this->makeBusiness();
            $service = $this->makeService($business);

            $phone = $this->randomPhone();
            $normalizedPhone = $this->normalizedPhone($phone);

            BookingVerification::create([
                'business_id' => $business->id,
                'service_id' => $service->id,
                'appointment_date' => now()->addDay()->toDateString(),
                'start_time' => '10:00',
                'end_time' => '10:30',
                'customer_name' => 'Test Customer',
                'customer_phone' => $normalizedPhone,
                'customer_email' => null,
                'delivery_channel' => $channel,
                'code_hash' => Hash::make('999999'),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]);

            for ($attempt = 1; $attempt <= 5; $attempt++) {
                $this->postJson(route('booking.verification.confirm', $business->slug), [
                    'phone' => $phone,
                    'code' => '111111',
                ])->assertStatus(422);
            }

            $this->postJson(route('booking.verification.confirm', $business->slug), [
                'phone' => $phone,
                'code' => '111111',
            ])->assertStatus(429);
        }
    }
}
