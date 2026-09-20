<?php

namespace Tests\Feature\BookingVerification;

use App\Models\Business;
use App\Models\Plan;
use App\Models\Service;
use Carbon\Carbon;

trait InteractsWithBookingVerification
{
    protected function makeBusiness(): Business
    {
        return Business::factory()->create();
    }

    protected function makePremiumBusiness(): Business
    {
        $plan = Plan::factory()->premium()->create();

        return Business::factory()->create(['plan_id' => $plan->id]);
    }

    protected function makeService(Business $business, string $confirmationMode = 'auto_confirmation'): Service
    {
        return Service::factory()->create([
            'business_id' => $business->id,
            'confirmation_mode' => $confirmationMode,
        ]);
    }

    protected function randomPhone(): string
    {
        return '05' . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    protected function normalizedPhone(string $rawPhone): string
    {
        return '+972' . substr($rawPhone, 1);
    }

    protected function sendPayload(Service $service, array $overrides = []): array
    {
        return array_merge([
            'service_id' => $service->id,
            'appointment_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'customer_name' => 'Test Customer',
            'customer_phone' => $this->randomPhone(),
            'customer_email' => null,
            'delivery_channel' => 'sms',
        ], $overrides);
    }
}
