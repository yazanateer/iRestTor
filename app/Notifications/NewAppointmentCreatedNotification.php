<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewAppointmentCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'business_id' => $this->appointment->business_id,
            'customer_name' => $this->appointment->customer_name,
            'customer_phone' => $this->appointment->customer_phone,
            'appointment_date' => $this->appointment->appointment_date,
            'start_time' => $this->appointment->start_time,
            'status' => $this->appointment->status,
            'message' => 'New appointment received.',
        ];
    }
}