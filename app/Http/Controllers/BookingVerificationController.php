<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\BookingVerification;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;


class BookingVerificationController extends Controller
{
    public function send(Request $request, Business $business, SmsService $smsService)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'customer_name' => ['required', 'string'],
            'customer_phone' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
        ]);

        $alreadyBooked = Appointment::query()
            ->where('business_id', $business->id)
            ->whereDate(
                'appointment_date',
                $validated['appointment_date']
            )
            ->where('start_time', $validated['start_time'])
            ->exists();
        if($alreadyBooked) {
            return response()->json([
                'message' => 'Slot already booked.'
            ], 422);
        }

        $code = random_int(100000, 999999);
        BookingVerification::query()
            ->where('customer_phone', $validated['customer_phone'])
            ->delete();
        
        BookingVerification::create([
            'business_id' => $business->id,
            'service_id' => $validated['service_id'],
            'appointment_date' =>
                $validated['appointment_date'],
            'start_time' =>
                $validated['start_time'],
            'end_time' =>
                $validated['end_time'],
            'customer_name' =>
                $validated['customer_name'],
            'customer_phone' =>
                $validated['customer_phone'],
            'customer_email' =>
                $validated['customer_email'],
            'code_hash' =>
                Hash::make($code),
            'expires_at' =>
                now()->addMinutes(5),
            'attempts' => 0,
        ]);

        $smsService->sendVerificationCode(
            $validated['customer_phone'],
            (string) $code
        );
        return response()->json([
            'success' => true,
        ]);
    }

    public function confirm(Request $request, Business $business)
    {
        $validated = $request->validate([
            'phone' => ['required'],
            'code' => ['required'],
        ]);

        $verification = BookingVerification::query()
            ->where('business_id', $business->id)
            ->where('customer_phone', $validated['phone'])
            ->latest()
            ->first();

        if (! $verification) {
            return response()->json([
                'message' => 'Verification not found.',
            ], 404);
        }

        if ($verification->isExpired()) {
            return response()->json([
                'message' => 'Code expired.',
            ], 422);
        }

        if ($verification->attempts >= 5) {
            return response()->json([
                'message' => 'Too many attempts.',
            ], 429);
        }

        $verification->increment('attempts');

        if (! Hash::check($validated['code'], $verification->code_hash)) {
            return response()->json([
                'message' => 'Invalid code.',
            ], 422);
        }

        $slotTaken = Appointment::query()
            ->where('business_id', $business->id)
            ->whereDate('appointment_date', $verification->appointment_date)
            ->where('start_time', $verification->start_time)
            ->exists();

        if ($slotTaken) {
            return response()->json([
                'message' => 'Slot already booked.',
            ], 422);
        }
        $service = $verification->service;
        $status = $service->confirmation_mode === 'requires_approval' ? 'pending_approval' : 'confirmed';
        DB::transaction(function () use ($verification, $business, $status) {
            Appointment::create([
                'business_id' => $business->id,
                'service_id' => $verification->service_id,
                'appointment_date' => $verification->appointment_date,
                'start_time' => $verification->start_time,
                'end_time' => $verification->end_time,
                'customer_name' => $verification->customer_name,
                'customer_phone' => $verification->customer_phone,
                'customer_email' => $verification->customer_email,
                'status' => $status,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
            ]);

            $verification->update([
                'verified_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'status' => $status,
            'requires_approval' => $status === 'pending_approval',
        ]);
    }
}