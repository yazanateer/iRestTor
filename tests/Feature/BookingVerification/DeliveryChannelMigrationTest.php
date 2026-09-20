<?php

namespace Tests\Feature\BookingVerification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeliveryChannelMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_verifications_table_has_delivery_channel_column(): void
    {
        $this->assertTrue(Schema::hasColumn('booking_verifications', 'delivery_channel'));
    }
}
