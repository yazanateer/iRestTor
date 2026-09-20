<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTrialActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = auth()->user()?->business;

        if (! $business) {
            abort(403, 'No business assigned to this user.');
        }

        if ($business->isPaid() || $business->onTrial()) {
            return $next($request);
        }

        return redirect()->route('plans');
    }
}
