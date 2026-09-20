<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\BookingVerification;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Jobs\SendOtpSmsJob;
use App\Jobs\SendOtpWhatsappJob;
use App\Notifications\NewAppointmentCreatedNotification;



class BookingVerificationController extends Controller
{
    /**
     * Twilio error codes for a WhatsApp recipient that is not reachable on WhatsApp
     * (undeliverable / opt-in required). Used to distinguish this failure so the
     * customer can be told to try SMS instead.
     */
    private const WHATSAPP_NUMBER_NOT_FOUND_CODES = [63016, 63024];

    public function send(Request $request, Business $business)
    {
        $this->ensureBusinessIsActive($business);
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'customer_name' => ['required', 'string'],
            'customer_phone' => ['required', 'string', 'regex:/^(05\d{8}|\+9725\d{8})$/'],
            'customer_email' => ['nullable', 'email'],
            'delivery_channel' => ['required', Rule::in(['sms', 'whatsapp'])],
        ]);

        $validated['customer_phone'] = $this->normalizeIsraeliPhone(
        $validated['customer_phone']
        );

        if ($validated['delivery_channel'] === 'whatsapp' && ! $business->canUseWhatsapp()) {
            return response()->json([
                'message' => __('booking.whatsappNotAvailable'),
            ], 422);
        }

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
            ->where('business_id', $business->id)
            ->where('customer_phone', $validated['customer_phone'])
            ->delete();
        
        $verification = BookingVerification::create([
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
            'delivery_channel' =>
                $validated['delivery_channel'],
            'code_hash' =>
                Hash::make($code),
            'expires_at' =>
                now()->addMinutes(5),
            'attempts' => 0,
        ]);

        try {
            match ($validated['delivery_channel']) {
                'sms' => SendOtpSmsJob::dispatchSync(
                    $validated['customer_phone'],
                    (string) $code
                ),
                'whatsapp' => SendOtpWhatsappJob::dispatchSync(
                    $validated['customer_phone'],
                    (string) $code
                ),
            };
        } catch (\Throwable $e) {
            $verification->delete();

            Log::error('OTP delivery failed', [
                'delivery_channel' => $validated['delivery_channel'],
                'phone' => $this->maskPhone($validated['customer_phone']),
                'error' => $e->getMessage(),
            ]);

            if (
                $validated['delivery_channel'] === 'whatsapp'
                && in_array($e->getCode(), self::WHATSAPP_NUMBER_NOT_FOUND_CODES, true)
            ) {
                return response()->json([
                    'message' => __('booking.whatsappNumberNotFound'),
                ], 500);
            }

            return response()->json([
                'message' => __('booking.otpDeliveryFailed'),
            ], 500);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function confirm(Request $request, Business $business)
    {
        $this->ensureBusinessIsActive($business);
        $validated = $request->validate([
            'phone' => ['required',
                        'string',
                        'regex:/^(05\d{8}|\+9725\d{8})$/',
                        ],
            'code' => ['required', 'digits:6'],
        ]);

        $phone = $this->normalizeIsraeliPhone($validated['phone']);
        $verification = BookingVerification::query()
            ->where('business_id', $business->id)
            ->where('customer_phone', $phone)
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
        $appointment = null;

        DB::transaction(function () use ($verification, $business, $status, &$appointment) {
            $appointment = Appointment::create([
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

        $business->users()->each(function ($manager) use ($appointment) {
            $manager->notify(new NewAppointmentCreatedNotification($appointment));
        });

        return response()->json([
            'success' => true,
            'status' => $status,
            'requires_approval' => $status === 'pending_approval',
        ]);
    }

    private function normalizeIsraeliPhone(String $phone) : string
    {
        $phone = preg_replace('/[\s-]/', '', $phone);

        if (str_starts_with($phone, '05')) {
            return '+972' . substr($phone, 1);
        }
        return $phone;
    }

    private function ensureBusinessIsActive(Business $business): void
        {
            abort_if(! $business->isActive(), 404);
        }

    private function maskPhone(string $phone): string
    {
        $length = strlen($phone);

        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4) . substr($phone, -4);
    }
}