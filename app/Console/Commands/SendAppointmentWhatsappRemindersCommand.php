<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentWhatsappReminderJob;
use App\Models\Appointment;
use Illuminate\Console\Command;

class SendAppointmentWhatsappRemindersCommand extends Command
{
    protected $signature = 'appointments:send-whatsapp-reminders';

    protected $description = 'Send WhatsApp reminders for upcoming confirmed appointments.';

    public function handle(): int
    {
        $appointments = Appointment::query()
            ->with(['business.plan', 'service'])
            ->where('status', 'confirmed')
            ->whereNull('whatsapp_reminder_sent_at')
            ->whereDate('appointment_date', now()->addDay()->toDateString())
            ->get();

        foreach ($appointments as $appointment) {
            if (! $appointment->business?->canUseWhatsapp()) {
                continue;
            }

            SendAppointmentWhatsappReminderJob::dispatch($appointment->id);

            $appointment->update([
                'whatsapp_reminder_sent_at' => now(),
            ]);
        }

        $this->info("Dispatched {$appointments->count()} WhatsApp reminder checks.");

        return self::SUCCESS;
    }
}