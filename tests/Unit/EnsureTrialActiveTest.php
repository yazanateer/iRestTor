<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureTrialActive;
use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureTrialActiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // A stand-in for the real `plans` route, registered by task 5.2.
        Route::get('/_test-plans', fn () => 'plans')->name('plans');
        Route::getRoutes()->refreshNameLookups();
    }

    private function passThrough(): Request
    {
        return Request::create('/dashboard', 'GET');
    }

    public function test_allows_paid_business_regardless_of_trial_ends_at(): void
    {
        $plan = Plan::factory()->create();
        $business = Business::factory()->create([
            'plan_id' => $plan->id,
            'trial_ends_at' => now()->subDays(10),
        ]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);
        $this->actingAs($manager);

        $response = (new EnsureTrialActive())->handle($this->passThrough(), fn ($req) => response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    public function test_allows_active_trial_business(): void
    {
        $business = Business::factory()->create([
            'plan_id' => null,
            'trial_ends_at' => now()->addDays(3),
        ]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);
        $this->actingAs($manager);

        $response = (new EnsureTrialActive())->handle($this->passThrough(), fn ($req) => response('ok'));

        $this->assertSame('ok', $response->getContent());
    }

    public function test_redirects_expired_trial_business_to_plans(): void
    {
        $business = Business::factory()->create([
            'plan_id' => null,
            'trial_ends_at' => now()->subDays(1),
        ]);
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => $business->id]);
        $this->actingAs($manager);

        $response = (new EnsureTrialActive())->handle($this->passThrough(), fn ($req) => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('plans'), $response->headers->get('Location'));
    }

    public function test_aborts_403_when_manager_has_no_business(): void
    {
        $manager = User::factory()->create(['role' => 'manager', 'business_id' => null]);
        $this->actingAs($manager);

        try {
            (new EnsureTrialActive())->handle($this->passThrough(), fn ($req) => response('ok'));
            $this->fail('Expected an HttpException to be thrown.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }
}
