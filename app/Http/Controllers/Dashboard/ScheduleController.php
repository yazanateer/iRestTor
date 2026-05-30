<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $business = auth()->user()->business;

        $selectedDate = $request->query(
            'date',
            today()->toDateString()
        );

        $dayOfWeek = Carbon::parse($selectedDate)->dayOfWeek;

        $availability = $business->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        $opensAt = $availability?->opens_at ?? '09:00';
        $closesAt = $availability?->closes_at ?? '17:00';

        $appointments = Appointment::query()
            ->with('service')
            ->where('business_id', $business->id)
            ->whereDate('appointment_date', $selectedDate)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        $bookedMinutes = $appointments->sum(function ($appointment) {
            return Carbon::parse($appointment->start_time)
                ->diffInMinutes(Carbon::parse($appointment->end_time));
        });

        $workingMinutes = Carbon::parse($opensAt)
            ->diffInMinutes(Carbon::parse($closesAt));

        $utilizationPercentage = $workingMinutes > 0
            ? min(100, round(($bookedMinutes / $workingMinutes) * 100))
            : 0;

        return Inertia::render('Dashboard/Schedule/Index', [
            'business' => $business,

            'selectedDate' => $selectedDate,

            'appointments' => $appointments,

            'workingHours' => [
                'opens_at' => $opensAt,
                'closes_at' => $closesAt,
            ],

            'stats' => [
                'totalAppointments' => $appointments->count(),
                'pendingAppointments' => $appointments
                    ->where('status', 'pending_approval')
                    ->count(),
                'bookedMinutes' => $bookedMinutes,
                'workingMinutes' => $workingMinutes,
                'utilizationPercentage' => $utilizationPercentage,
            ],
        ]);
    }
}