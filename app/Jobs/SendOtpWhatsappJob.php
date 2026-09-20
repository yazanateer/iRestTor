<?php

namespace App\Jobs;

use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOtpWhatsappJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $phone,
        public string $code,
    ) {
    }

    public function handle(WhatsappService $whatsapp): void
    {
        $whatsapp->sendOtpCode(
            $this->phone,
            $this->code
        );
    }
}
