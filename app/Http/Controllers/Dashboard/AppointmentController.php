<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SmsService;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $businessId = auth()->user()->business_id;

        $baseQuery = Appointment::query()
            ->where('business_id', $businessId);

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending_approval' => (clone $baseQuery)->where('status', 'pending_approval')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        $query = Appointment::query()
            ->with('service')
            ->where('business_id', $businessId);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $appointments = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'pending_approval' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard/Appointments/Index', [
            'appointments' => $appointments,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
            'counts' => $counts,
        ]);
    }

    // public function index(Request $request)
    // {
    //     $status = $request->query('status', 'all');
    //     $query = Appointment::query()
    //         ->with('service')
    //         ->where('business_id', auth()->user()->business_id);
    //     if ($status !== 'all') {
    //         $query->where('status', $status);
    //     }
    //     $appointments = $query
    //         ->orderByRaw("
    //             CASE
    //                 WHEN status = 'pending_approval' THEN 0
    //                 ELSE 1
    //             END
    //         ")
    //         ->orderBy('appointment_date')
    //         ->orderBy('start_time')
    //         ->paginate(10)
    //         ->withQueryString();
    //     return Inertia::render('Dashboard/Appointments/Index', [
    //         'appointments' => $appointments,
    //         'filters' => [
    //             'status' => $status,
    //         ],
    //     ]);
    // }

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
