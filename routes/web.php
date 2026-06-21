<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingVerificationController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ContactRequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [LandingPageController::class, 'index'])
    ->name('landing');

Route::post('/contact-requests', [ContactRequestController::class, 'store'])
    ->name('contact-requests.store');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/book/{business:slug}')
    ->name('booking.')
    ->group(function () {
        Route::get('/', [BookingController::class, 'show'])->name('show');
        Route::get('/slots', [BookingController::class, 'slots'])->middleware('throttle:booking-slots')->name('slots');
        Route::post('/appointments', [BookingController::class, 'store'])->middleware('throttle:booking-create')->name('appointments.store');
        Route::post('/verification/send', [BookingVerificationController::class, 'send'])->middleware('throttle:otp-send')->name('verification.send');
        Route::post('/verification/confirm', [BookingVerificationController::class, 'confirm'])->middleware('throttle:otp-confirm')->name('verification.confirm');
    });



require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/dashboard.php';