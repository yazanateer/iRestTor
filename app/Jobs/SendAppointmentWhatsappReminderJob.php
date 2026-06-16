<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsappService;

class SendAppointmentWhatsappReminderJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public int $appointmentId
    ) {
    }

    public function handle(WhatsappService $whatsappService): void
    {
        $appointment = Appointment::query()
            ->with(['business', 'service'])
            ->find($this->appointmentId);

        if (! $appointment) {
            return;
        }

        Log::info('WhatsApp reminder job ready', [
            'appointment_id' => $appointment->id,
            'customer_phone' => $appointment->customer_phone,
            'business_id' => $appointment->business_id,
        ]);

        $whatsappService->sendAppointmentReminder($appointment);
    }
}