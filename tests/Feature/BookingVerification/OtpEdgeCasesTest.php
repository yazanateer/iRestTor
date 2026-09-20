<?php

namespace Tests\Feature\BookingVerification;

use App\Models\Appointment;
use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OtpEdgeCasesTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Notification::fake();
    }

    private function createVerification(array $overrides = []): array
    {
        $business = $this->makeBusiness();
        $service = $this->makeService($business);
        $phone = $this->randomPhone();
        $normalizedPhone = $this->normalizedPhone($phone);

        $verification = BookingVerification::create(array_merge([
            'business_id' => $business->id,
            'service_id' => $service->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'customer_name' => 'Test Customer',
            'customer_phone' => $normalizedPhone,
            'customer_email' => null,
            'delivery_channel' => 'sms',
            'code_hash' => Hash::make('654321'),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ], $overrides));

        return [$business, $verification, $phone];
    }

    /**
     * Req 12.5: Expired OTP -> 422, no Appointment created.
     */
    public function test_expired_otp_is_rejected(): void
    {
        [$business, , $phone] = $this->createVerification([
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson(route('booking.verification.confirm', $business->slug), [
            'phone' => $phone,
            'code' => '654321',
        ])->assertStatus(422);

        $this->assertSame(0, Appointment::query()->where('business_id', $business->id)->count());
    }

    /**
     * Req 12.6: Incorrect OTP -> 422, attempts incremented, no Appointment created.
     */
    public function test_incorrect_otp_increments_attempts_and_is_rejected(): void
    {
        [$business, $verification, $phone] = $this->createVerification();

        $this->postJson(route('booking.verification.confirm', $business->slug), [
            'phone' => $phone,
            'code' => '000000',
        ])->assertStatus(422);

        $this->assertSame(1, $verification->refresh()->attempts);
        $this->assertSame(0, Appointment::query()->where('business_id', $business->id)->count());
    }

    /**
     * Req 12.7: Exceeded attempt limit (6th confirm after 5 failed attempts) -> 429, no Appointment.
     */
    public function test_exceeded_attempt_limit_is_rejected(): void
    {
        [$business, , $phone] = $this->createVerification(['attempts' => 5]);

        $this->postJson(route('booking.verification.confirm', $business->slug), [
            'phone' => $phone,
            'code' => '654321',
        ])->assertStatus(429);

        $this->assertSame(0, Appointment::query()->where('business_id', $business->id)->count());
    }
}
