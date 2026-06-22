<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\ContactRequestController;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function() {
        Route::get('/dashboard', function() {
            return Inertia::render('Admin/Dashboard');
        })->name('dashboard');

        Route::resource('businesses', BusinessController::class);
        Route::resource('managers', ManagerController::class);
        Route::get('/contact-requests', [ContactRequestController::class, 'index'])
            ->name('contact-requests.index');
        Route::patch( '/contact-requests/{contactRequest}/status',[ContactRequestController::class, 'updateStatus'])
            ->name('contact-requests.status');

    });