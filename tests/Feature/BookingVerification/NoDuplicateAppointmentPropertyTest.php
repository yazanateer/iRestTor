<?php

namespace Tests\Feature\BookingVerification;

use App\Models\Appointment;
use App\Models\BookingVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NoDuplicateAppointmentPropertyTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithBookingVerification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        Notification::fake();
    }

    /**
     * Feature: otp-delivery-channel, Property 9: At most one appointment per verification.
     * For any valid verification, a successful confirm SHALL create exactly one Appointment,
     * and any subsequent confirm for the same slot SHALL be rejected as slot-taken — never
     * producing a duplicate Appointment.
     * Validates: Requirements 9.7, 10.5
     */
    public function test_double_confirm_creates_exactly_one_appointment(): void
    {
        foreach (['sms', 'whatsapp'] as $channel) {
            $business = $this->makeBusiness();
            $service = $this->makeService($business);

            $phone = $this->randomPhone();
            $normalizedPhone = $this->normalizedPhone($phone);
            $code = '123456';

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
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
            ]);

            $first = $this->postJson(route('booking.verification.confirm', $business->slug), [
                'phone' => $phone,
                'code' => $code,
            ]);
            $first->assertStatus(200)->assertJson(['success' => true]);

            $second = $this->postJson(route('booking.verification.confirm', $business->slug), [
                'phone' => $phone,
                'code' => $code,
            ]);
            $second->assertStatus(422);

            $this->assertSame(
                1,
                Appointment::query()->where('business_id', $business->id)->count()
            );
        }
    }
}
