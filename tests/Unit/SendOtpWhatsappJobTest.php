<?php

namespace Tests\Unit;

use App\Jobs\SendOtpWhatsappJob;
use Tests\TestCase;

class SendOtpWhatsappJobTest extends TestCase
{
    public function test_tries_is_set_to_three(): void
    {
        $job = new SendOtpWhatsappJob('+972501234567', '123456');

        $this->assertSame(3, $job->tries);
    }
}
