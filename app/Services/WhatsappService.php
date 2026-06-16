<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class WhatsappService
{
    public function sendAppointmentReminder(Appointment $appointment): void
    {
        $message = $this->buildReminderMessage($appointment);

        $response = Http::asForm()
            ->withBasicAuth(
                config('services.twilio.sid'),
                config('services.twilio.token')
            )
            ->post(
                'https://api.twilio.com/2010-04-01/Accounts/' .
                config('services.twilio.sid') .
                '/Messages.json',
                [
                    'From' => config('services.twilio.whatsapp_from'),
                    'To' => 'whatsapp:' . $appointment->customer_phone,
                    'Body' => $message,
                ]
            );

        if (! $response->successful()) {
            Log::error('WhatsApp reminder failed', [
                'appointment_id' => $appointment->id,
                'phone' => $appointment->customer_phone,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            throw new RuntimeException('Failed to send WhatsApp reminder.');
        }

        Log::info('WhatsApp reminder sent successfully', [
            'appointment_id' => $appointment->id,
            'phone' => $appointment->customer_phone,
            'response' => $response->json(),
        ]);
    }

    private function buildReminderMessage(Appointment $appointment): string
    {
        return "Hello {$appointment->customer_name}, this is a reminder for your appointment at {$appointment->business->name} on {$appointment->appointment_date} at {$appointment->start_time}.";
    }
}