<?php

namespace Tests\Feature\BookingVerification;

use App\Models\BookingVerification;
use App\Models\User;
use App\Notifications\NewAppointmentCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ChannelAgnosticVerificationPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Feature: otp-delivery-channel, Property 3: Verification is channel-agnostic. For any
     * valid BookingVerification, the outcome of confirm (success/failure and whether an
     * Appointment is created) SHALL be identical whether the stored delivery_channel is sms
     * or whatsapp, and confirm SHALL never read a delivery_channel field from the request.
     * Validates: Requirements 3.6, 6.3, 9.5, 9.6
     */
    public function test_verification_is_channel_agnostic(): void
    {
        Notification::fake();

        foreach (['sms', 'whatsapp'] as $channel) {
            $business = $this->makeBusiness();
            $manager = User::factory()->create(['business_id' => $business->id]);
            $service = $this->makeService($business);

            $phone = $this->randomPhone();
            $normalizedPhone = $this->normalizedPhone($phone);
            $code = (string) random_int(100000, 999999);

            $verification = BookingVerification::create([
                'business_id' => $business->id,
                'service_id' => $service->id,
                'appointment_date' => now()->addDay()->toDateString(),
                'start_time' => '10:00',
                'end_time' => '10:30',
                'customer_name' => 'Test Customer',
                'customer_phone' => $normalizedPhone,
                'customer_email' => null,
                'delivery_channel' => $channel,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]);

            $response = $this->postJson(route('booking.verification.confirm', $business->slug), [
                'phone' => $phone,
                'code' => $code,
            ]);

            $response->assertStatus(200)->assertJson(['success' => true]);

            $this->assertDatabaseHas('appointments', [
                'business_id' => $business->id,
                'service_id' => $service->id,
                'customer_phone' => $normalizedPhone,
            ]);

            $this->assertNotNull($verification->refresh()->verified_at);

            Notification::assertSentTo($manager, NewAppointmentCreatedNotification::class);
        }
    }
}
