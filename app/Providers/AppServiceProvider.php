<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    RateLimiter::for('otp-send', function (Request $request) {
        return [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ];
    });

    RateLimiter::for('otp-confirm', function (Request $request) {
        return [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perHour(20)->by($request->ip()),
        ];
    });

    RateLimiter::for('booking-create', function (Request $request) {
        return [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(20)->by($request->ip()),
        ];
    });

    RateLimiter::for('booking-slots', function (Request $request) {
        return [
            Limit::perMinute(30)->by($request->ip()),
        ];
    });
}
}
