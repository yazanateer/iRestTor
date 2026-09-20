<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use Inertia\Inertia;
use App\Models\Appointment;
use Carbon\Carbon;


class BookingController extends Controller
{
  public function show(Business $business)
    {   
        $this->ensureBusinessIsActive($business);
        $business->load(['services', 'branding']);
        return Inertia::render('Booking/Show', [
            'business' => $business,
            'services' => $business->services()
                ->where('is_active', true)
                ->get(),
            'dateOverrides' => $business->dateOverrides()
                ->get(['date', 'is_active'])
                ->map(fn ($override) => [
                    'date' => $override->date,
                    'is_active' => $override->is_active,
                ]),
            'availabilityDays' => $business->availabilities()
                ->where('is_active', true)
                ->pluck('day_of_week')
                ->values(),
            'bookingWindowDays' => $business->booking_window_days ?? 14,
            'whatsappEnabled' => $business->canUseWhatsapp(),
            'branding' => $business->branding ? [
            ...$business->branding->toArray(),
            'logo_url' => $business->branding->logo_path
                ? asset('storage/' . $business->branding->logo_path)
                : null,
            'cover_image_url' => $business->branding->cover_image_path
                ? asset('storage/' . $business->branding->cover_image_path)
                : null,
        ] : null,
        ]);
    }
    
    public function slots(Request $request, Business $business)
    {
        $this->ensureBusinessIsActive($business);

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'date' => ['required', 'date'],
        ]);

        $service = $business->services()
            ->where('id', $validated['service_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $date = Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeek; // 0 Sunday - 6 Saturday

        // 1. Resolve business working hours
        $dateOverride = $business->dateOverrides()
            ->where('date', $date->toDateString())
            ->first();

        if ($dateOverride) {
            if (! $dateOverride->is_active) {
                return response()->json([
                    'slots' => [],
                ]);
            }

            $businessStart = Carbon::parse($date->toDateString() . ' ' . $dateOverride->start_time);
            $businessEnd = Carbon::parse($date->toDateString() . ' ' . $dateOverride->end_time);
        } else {
            $availability = $business->availabilities()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (! $availability) {
                return response()->json([
                    'slots' => [],
                ]);
            }

            $businessStart = Carbon::parse($date->toDateString() . ' ' . $availability->start_time);
            $businessEnd = Carbon::parse($date->toDateString() . ' ' . $availability->end_time);
        }

        // 2. Get existing appointments
        $existingAppointments = Appointment::where('business_id', $business->id)
            ->where('appointment_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['start_time', 'end_time']);

        // 3. Get breaks
        $breaks = $business->availabilityBreaks()
            ->where(function ($query) use ($date, $dayOfWeek) {
                $query->where('date', $date->toDateString())
                    ->orWhere(function ($query) use ($dayOfWeek) {
                        $query->whereNull('date')
                            ->where('day_of_week', $dayOfWeek);
                    });
            })
            ->get();

        // 4. Generate slots
        $slots = [];
        $start = $businessStart->copy();
        $end = $businessEnd->copy();

        while ($start->copy()->addMinutes($service->duration_minutes)->lte($end)) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($service->duration_minutes);

            $hasAppointmentConflict = $existingAppointments->contains(function ($appointment) use ($slotStart, $slotEnd, $date) {
                $appointmentStart = Carbon::parse($date->toDateString() . ' ' . $appointment->start_time);
                $appointmentEnd = Carbon::parse($date->toDateString() . ' ' . $appointment->end_time);

                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            $hasBreakConflict = $breaks->contains(function ($break) use ($slotStart, $slotEnd, $date) {
                $breakStart = Carbon::parse($date->toDateString() . ' ' . $break->start_time);
                $breakEnd = Carbon::parse($date->toDateString() . ' ' . $break->end_time);

                return $slotStart->lt($breakEnd) && $slotEnd->gt($breakStart);
            });

            if (! $hasAppointmentConflict && ! $hasBreakConflict) {
                $slots[] = [
                    'start_time' => $slotStart->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                ];
            }

            $start->addMinutes($service->duration_minutes);
        }

        return response()->json([
            'slots' => $slots,
        ]);
}

    private function ensureBusinessIsActive(Business $business): void
    {
        abort_if(! $business->isActive(), 404);
    }
}
