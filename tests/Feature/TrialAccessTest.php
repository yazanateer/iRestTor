<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialAccessTest extends TestCase
{
    use RefreshDatabase;

    // Requirement 11.3: a manager of an Active_Trial business can access the dashboard.
    public function test_active_trial_manager_can_access_dashboard(): void
    {
        $business = Business::factory()->trial()->create();
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);

        $this->actingAs($manager)->get('/dashboard')->assertStatus(200);
        $this->actingAs($manager)->get('/dashboard/services')->assertStatus(200);
    }

    // Requirement 11.4: a manager of an Expired_Trial business is blocked from the
    // standard dashboard routes and redirected to the Plan_Selection_Screen.
    public function test_expired_trial_manager_is_blocked_and_redirected_to_plans(): void
    {
        $business = Business::factory()->create(['plan_id' => null, 'trial_ends_at' => now()->subDay()]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);

        foreach (['/dashboard', '/dashboard/services', '/dashboard/availability', '/dashboard/appointments', '/dashboard/schedule'] as $route) {
            $this->actingAs($manager)->get($route)->assertRedirect(route('plans'));
        }
    }
}
