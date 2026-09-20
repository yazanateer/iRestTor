<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    // Requirement 11.5 / 7.1–7.5: a manager cannot access, modify, or delete a
    // resource belonging to a different business_id.
    public function test_manager_cannot_edit_update_or_delete_another_businesses_service(): void
    {
        $businessA = Business::factory()->trial()->create();
        $managerA = User::factory()->create(['role' => 'manager', 'business_id' => $businessA->id]);

        $businessB = Business::factory()->trial()->create();
        $serviceB = Service::factory()->create(['business_id' => $businessB->id, 'name' => 'Original Name']);

        $this->actingAs($managerA)->get("/dashboard/services/{$serviceB->id}/edit")->assertStatus(403);

        $this->actingAs($managerA)->put("/dashboard/services/{$serviceB->id}", [
            'name' => 'Hijacked Name',
            'duration_minutes' => 30,
            'confirmation_mode' => 'auto_confirm',
        ])->assertStatus(403);

        $this->actingAs($managerA)->delete("/dashboard/services/{$serviceB->id}")->assertStatus(403);

        $this->assertSame('Original Name', $serviceB->fresh()->name);
        $this->assertNotNull(Service::find($serviceB->id));
    }
}
