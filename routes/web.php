<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingVerificationController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', [LandingPageController::class, 'index'])
    ->name('landing');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('/book/{business:slug}')
    ->name('booking.')
    ->group(function () {
        Route::get('/', [BookingController::class, 'show'])->name('show');
        Route::get('/slots', [BookingController::class, 'slots'])->name('slots');
        Route::post('/appointments', [BookingController::class, 'store'])->name('appointments.store');
        Route::post('/verification/send', [BookingVerificationController::class, 'send'])->name('verification.send');
        Route::post('/verification/confirm', [BookingVerificationController::class, 'confirm'])->name('verification.confirm');
    });



require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/dashboard.php';