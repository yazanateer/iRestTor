<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Services/Index', [
            'services' => Service::where('business_id', auth()->user()->business_id)
                ->latest()
                ->get(),
        ]);
    }

    public function create()
    {
        $business = auth()->user()->business;
        $business->loadMissing('plan');

        return Inertia::render('Dashboard/Services/Create', [
            'features' => [
                'approvalWorkflow' => $business->canUseApprovalWorkflow(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $business = auth()->user()->business;
        $business->loadMissing('plan');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'confirmation_mode' => ['required', 'in:auto_confirm,requires_approval'],
        ]);

        if (! $business->canUseApprovalWorkflow()) {
            $validated['confirmation_mode'] = 'auto_confirm';
        }

        $validated['business_id'] = $business->id;

        Service::create($validated);

        return redirect()
            ->route('dashboard.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $this->authorizeBusinessService($service);

        $business = auth()->user()->business;
        $business->loadMissing('plan');

        return Inertia::render('Dashboard/Services/Edit', [
            'service' => $service,
            'features' => [
                'approvalWorkflow' => $business->canUseApprovalWorkflow(),
            ],
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeBusinessService($service);

        $business = auth()->user()->business;
        $business->loadMissing('plan');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'confirmation_mode' => ['required', 'in:auto_confirm,requires_approval'],
        ]);

        if (! $business->canUseApprovalWorkflow()) {
            $validated['confirmation_mode'] = 'auto_confirm';
        }

        $service->update($validated);

        return redirect()
            ->route('dashboard.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $this->authorizeBusinessService($service);

        $service->delete();

        return redirect()
            ->route('dashboard.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    private function authorizeBusinessService(Service $service): void
    {
        abort_if($service->business_id !== auth()->user()->business_id, 403);
    }
}