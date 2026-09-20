<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\BusinessBrandingSettings;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function validBusinessPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Admin Created Salon',
            'timezone' => 'Asia/Jerusalem',
            'is_active' => true,
            'primary_color' => '#123456',
            'secondary_color' => '#654321',
            'accent_color' => '#abcdef',
            'theme_style' => 'modern',
        ], $overrides);
    }

    // EXAMPLE 4.4 / 9.3: admin-created business is a Paid_Business
    // (trial_ends_at null, plan_id non-null) and its manager reaches the dashboard.
    public function test_admin_created_business_resolves_to_paid_business_and_manager_reaches_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $plan = Plan::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/businesses', $this->validBusinessPayload([
            'plan_id' => $plan->id,
        ]));

        $response->assertRedirect(route('admin.businesses.index'));

        $business = Business::first();
        $this->assertNotNull($business);
        $this->assertNull($business->trial_ends_at);
        $this->assertSame($plan->id, $business->plan_id);
        $this->assertTrue($business->isPaid());
        $this->assertFalse($business->onTrial());
        $this->assertFalse($business->trialExpired());

        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);
        $this->actingAs($manager)->get('/dashboard')->assertStatus(200);
    }

    // EXAMPLE 6.2: expired-trial manager can still reach the plan-selection screen (not redirected further).
    public function test_expired_trial_manager_can_view_plans_screen(): void
    {
        $business = Business::factory()->create(['plan_id' => null, 'trial_ends_at' => now()->subDay()]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);

        $this->actingAs($manager)->get(route('plans'))->assertStatus(200);
    }

    // EXAMPLE 8.4: premium business capability gates remain unchanged by this feature.
    public function test_premium_business_capability_gates_still_return_true(): void
    {
        $business = Business::factory()->premium()->create();

        $this->assertTrue($business->isPremium());
        $this->assertTrue($business->canUseApprovalWorkflow());
        $this->assertTrue($business->canUseWhatsapp());
        $this->assertTrue($business->canUseReminders());
    }

    // EXAMPLE 9.1: admin store still creates a business and its branding.
    public function test_admin_store_still_creates_business_and_branding(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $plan = Plan::factory()->create();

        $this->actingAs($admin)->post('/admin/businesses', $this->validBusinessPayload([
            'plan_id' => $plan->id,
        ]));

        $business = Business::first();
        $this->assertNotNull($business);
        $this->assertSame(1, BusinessBrandingSettings::where('business_id', $business->id)->count());
    }

    // EXAMPLE 9.2: admin store rejects a missing plan_id.
    public function test_admin_store_rejects_missing_plan_id(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->from('/admin/businesses/create')
            ->actingAs($admin)
            ->post('/admin/businesses', $this->validBusinessPayload());

        $response->assertSessionHasErrors('plan_id');
        $this->assertSame(0, Business::count());
    }
}
