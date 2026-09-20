<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationGateTest extends TestCase
{
    use RefreshDatabase;

    // Requirement 11.6 / 12.3: an unverified manager is redirected to the
    // email-verification notice when requesting a dashboard route.
    public function test_unverified_manager_is_redirected_to_verification_notice(): void
    {
        $business = Business::factory()->trial()->create();
        $manager = User::factory()->unverified()->create(['role' => 'manager', 'business_id' => $business->id]);

        $this->actingAs($manager)->get('/dashboard')->assertRedirect(route('verification.notice'));
    }

    // Requirement 11.6 / 12.4: a verified manager reaches the dashboard
    // (subject to trial state).
    public function test_verified_manager_reaches_dashboard(): void
    {
        $business = Business::factory()->trial()->create();
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);

        $this->actingAs($manager)->get('/dashboard')->assertStatus(200);
    }

    // Requirement 11.6: a verified manager whose trial has expired is still
    // redirected to plan selection, not the verification notice.
    public function test_verified_manager_with_expired_trial_is_redirected_to_plans(): void
    {
        $business = Business::factory()->create(['plan_id' => null, 'trial_ends_at' => now()->subDay()]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);

        $this->actingAs($manager)->get('/dashboard')->assertRedirect(route('plans'));
    }
}
