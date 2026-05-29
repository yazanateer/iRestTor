<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SmsService;

class AppointmentController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;

        return Inertia::render('Dashboard/Appointments/Index', [
            'appointments' => Appointment::with('service')
                ->where('business_id', $businessId)
                ->latest('appointment_date')
                ->get(),
        ]);
    }

    public function confirm(Appointment $appointment, SmsService $smsService)
    {
        abort_if(
            $appointment->business_id !== auth()->user()->business_id,
            403
        );
        abort_if(
            $appointment->status !== 'pending_approval',
            422
        );

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'cancelled_at' => null,
        ]);

        $smsService->sendMessage(
        $appointment->customer_phone,
        "Your appointment has been confirmed by the manager."
        );

        return back()->with('success', 'Appointment confirmed successfully.');

    }

    public function reject(Appointment $appointment, SmsService $smsService)
    {
        abort_if(
            $appointment->business_id !== auth()->user()->business_id,
            403
        );
        abort_if(
            $appointment->status !== 'pending_approval',
            422
        );

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $smsService->sendMessage(
            $appointment->customer_phone,
            "Your appointment request was rejected. Please choose another time."
        );

        return back()->with('success', 'Appointment rejected successfully.');
    }
}
