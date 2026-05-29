<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\AvailabilityController;
use App\Http\Controllers\Dashboard\AppointmentController;

Route::middleware(['auth', 'manager'])
    ->get('/dashboard', function() {
        $user = auth()->user();

        return Inertia::render('Dashboard/Index', [
            'business' => $user->business,
            'bookingLink' => url('/book/' . $user->business->slug),
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
    });


