<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOtpSmsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $phone,
        public string $code,
    ) {
    }

    public function handle(SmsService $smsService): void
    {
        $smsService->sendVerificationCode(
            $this->phone,
            $this->code
        );
    }
}
