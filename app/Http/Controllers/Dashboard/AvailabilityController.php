<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BusinessAvailability;
use App\Models\BusinessAvailabilityBreak;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AvailabilityController extends Controller
{
    public function index()
    {
        $business = auth()->user()->business;
        $businessId = $business->id;

        $existing = BusinessAvailability::where('business_id', $businessId)
            ->get()
            ->keyBy('day_of_week');

        $days = collect([
            ['day_of_week' => 0, 'label' => 'Sunday'],
            ['day_of_week' => 1, 'label' => 'Monday'],
            ['day_of_week' => 2, 'label' => 'Tuesday'],
            ['day_of_week' => 3, 'label' => 'Wednesday'],
            ['day_of_week' => 4, 'label' => 'Thursday'],
            ['day_of_week' => 5, 'label' => 'Friday'],
            ['day_of_week' => 6, 'label' => 'Saturday'],
        ])->map(function ($day) use ($existing) {
            $availability = $existing->get($day['day_of_week']);

            return [
                'day_of_week' => $day['day_of_week'],
                'label' => $day['label'],
                'is_active' => $availability?->is_active ?? false,
                'start_time' => $availability?->start_time ? substr($availability->start_time, 0, 5) : '09:00',
                'end_time' => $availability?->end_time ? substr($availability->end_time, 0, 5) : '17:00',
            ];
        });

        $breaks = BusinessAvailabilityBreak::where('business_id', $businessId)
            ->whereNull('date')
            ->get()
            ->map(function ($break) {
                return [
                    'id' => $break->id,
                    'day_of_week' => $break->day_of_week,
                    'date' => $break->date,
                    'start_time' => substr($break->start_time, 0, 5),
                    'end_time' => substr($break->end_time, 0, 5),
                ];
            });

        $dateOverrides = $business
            ->dateOverrides()
            ->orderBy('date')
            ->get()
            ->map(fn ($override) => [
                'id' => $override->id,
                'date' => $override->date,
                'is_active' => $override->is_active,
                'start_time' => $override->start_time ? substr($override->start_time, 0, 5) : '09:00',
                'end_time' => $override->end_time ? substr($override->end_time, 0, 5) : '17:00',
            ]);

        return Inertia::render('Dashboard/Availability/Index', [
            'days' => $days,
            'breaks' => $breaks,
            'dateOverrides' => $dateOverrides,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'days' => ['required', 'array', 'size:7'],
            'days.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'days.*.is_active' => ['required', 'boolean'],
            'days.*.start_time' => ['nullable', 'date_format:H:i'],
            'days.*.end_time' => ['nullable', 'date_format:H:i'],

            'breaks' => ['nullable', 'array'],
            'breaks.*.id' => ['nullable', 'integer'],
            'breaks.*.day_of_week' => ['nullable', 'integer', 'between:0,6'],
            'breaks.*.date' => ['nullable', 'date'],
            'breaks.*.start_time' => ['required_with:breaks', 'date_format:H:i'],
            'breaks.*.end_time' => ['required_with:breaks', 'date_format:H:i'],

            'dateOverrides' => ['nullable', 'array'],
            'dateOverrides.*.id' => ['nullable', 'integer'],
            'dateOverrides.*.date' => ['required', 'date'],
            'dateOverrides.*.is_active' => ['required', 'boolean'],
            'dateOverrides.*.start_time' => ['nullable', 'date_format:H:i'],
            'dateOverrides.*.end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $business = auth()->user()->business;
        $businessId = $business->id;

        $daysByWeek = collect($validated['days'])->keyBy('day_of_week');

        foreach ($validated['breaks'] ?? [] as $break) {
            $day = $daysByWeek->get($break['day_of_week']);

            if (! $day || ! $day['is_active']) {
                return back()->withErrors([
                    'breaks' => 'Breaks can only be added to open working days.',
                ]);
            }

            if (
                $break['start_time'] < $day['start_time'] ||
                $break['end_time'] > $day['end_time'] ||
                $break['start_time'] >= $break['end_time']
            ) {
                return back()->withErrors([
                    'breaks' => 'Break times must be inside working hours and start before end.',
                ]);
            }
        }

        foreach ($validated['days'] as $day) {
            BusinessAvailability::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'day_of_week' => $day['day_of_week'],
                ],
                [
                    'is_active' => $day['is_active'],
                    'start_time' => $day['is_active'] ? $day['start_time'] : null,
                    'end_time' => $day['is_active'] ? $day['end_time'] : null,
                ]
            );
        }

        BusinessAvailabilityBreak::where('business_id', $businessId)
            ->whereNull('date')
            ->delete();

        foreach ($validated['breaks'] ?? [] as $break) {
            BusinessAvailabilityBreak::create([
                'business_id' => $businessId,
                'day_of_week' => $break['day_of_week'],
                'date' => null,
                'start_time' => $break['start_time'],
                'end_time' => $break['end_time'],
            ]);
        }

        $savedOverrideIds = [];

        foreach ($validated['dateOverrides'] ?? [] as $override) {
            if (
                $override['is_active'] &&
                (
                    empty($override['start_time']) ||
                    empty($override['end_time']) ||
                    $override['start_time'] >= $override['end_time']
                )
            ) {
                return back()->withErrors([
                    'dateOverrides' => 'Special date times are invalid.',
                ]);
            }

            $savedOverride = $business
                ->dateOverrides()
                ->updateOrCreate(
                    [
                        'date' => $override['date'],
                    ],
                    [
                        'is_active' => $override['is_active'],
                        'start_time' => $override['is_active'] ? $override['start_time'] : null,
                        'end_time' => $override['is_active'] ? $override['end_time'] : null,
                    ]
                );

            $savedOverrideIds[] = $savedOverride->id;
        }

        $business
            ->dateOverrides()
            ->whereNotIn('id', $savedOverrideIds)
            ->delete();

        return redirect()
            ->route('dashboard.availability.index')
            ->with('success', 'Availability updated successfully.');
    }
}