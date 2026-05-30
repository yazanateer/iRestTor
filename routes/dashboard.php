<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\AvailabilityController;
use App\Http\Controllers\Dashboard\AppointmentController;
use App\Http\Controllers\Dashboard\ScheduleController;
use App\Models\Appointment;
use App\Models\Service;

Route::middleware(['auth', 'manager'])
    ->get('/dashboard', function() {
        $user = auth()->user();
        $business = $user->business;

        return Inertia::render('Dashboard/Index', [
            'business' => $business,
            'bookingLink' => url('/book/' . $business->slug),
            'stats' => [
                'todayAppointments' => Appointment::where('business_id', $business->id)
                    ->whereDate('appointment_date', today())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'pendingRequests' => Appointment::where('business_id', $business->id)
                    ->where('status', 'pending_approval')
                    ->count(),
                'weekAppointments' => Appointment::where('business_id', $business->id)
                    ->whereBetween('appointment_date', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])
                    ->count(),
                'totalServices' => Service::where('business_id', $business->id)
                    ->count(),
            ],
            'todayAppointmentsList' => Appointment::query()
                ->with('service')
                ->where('business_id', $business->id)
                ->whereDate('appointment_date', today())
                ->where('status', '!=', 'cancelled')
                ->orderBy('start_time')
                ->get(),
            ]);
    })
    ->name('dashboard');

Route::middleware(['auth', 'manager'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::resource('services', ServiceController::class);
        Route::get('/availability', [AvailabilityController::class, 'index'])
            ->name('availability.index');
        Route::put('/availability', [AvailabilityController::class, 'update'])
            ->name('availability.update');
        Route::get('/appointments', [AppointmentController::class, 'index'])
            ->name('appointments.index');
        Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])
            ->name('appointments.confirm');
        Route::patch('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])
            ->name('appointments.reject');
        Route::get('/schedule', [ScheduleController::class, 'index'])
            ->name('schedule.index');
    });


