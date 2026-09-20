<?php

namespace Tests\Feature\Auth;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Manager',
            'business_name' => "Jane's Salon",
            'email' => 'jane@example.com',
            'phone' => '0501234567',
            'timezone' => 'Asia/Jerusalem',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => true,
        ], $overrides);
    }

    // EXAMPLE 1.1: guest GET /register → 200
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // EXAMPLE 1.7: valid POST → redirect to route('dashboard')
    public function test_valid_registration_redirects_to_dashboard(): void
    {
        Notification::fake();

        $response = $this->post('/register', $this->validPayload());

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    // Requirement 11.1: valid registration creates a Business + manager User,
    // linked, with plan_id null and trial_ends_at 7 days ahead of registration.
    public function test_valid_registration_creates_correctly_linked_active_trial_records(): void
    {
        Notification::fake();
        $this->travelTo(now());

        $this->post('/register', $this->validPayload());

        $this->assertSame(1, Business::count());
        $this->assertSame(1, User::count());

        $business = Business::first();
        $user = User::first();

        $this->assertSame('manager', $user->role);
        $this->assertSame($business->id, $user->business_id);
        $this->assertNull($business->plan_id);
        $this->assertTrue($business->onTrial());
        $this->assertEqualsWithDelta(now()->addDays(7)->timestamp, $business->trial_ends_at->timestamp, 2);
        $this->assertNotNull($business->tos_accepted_at);
        $this->assertAuthenticatedAs($user);
    }

    // Requirement 11.2: missing a required field is rejected with no records created.
    public function test_registration_missing_a_required_field_is_rejected(): void
    {
        Notification::fake();

        foreach (['name', 'business_name', 'email', 'timezone', 'password'] as $field) {
            $payload = $this->validPayload();
            unset($payload[$field]);

            $response = $this->from('/register')->post('/register', $payload);

            $response->assertSessionHasErrors($field);
        }

        $this->assertSame(0, Business::count());
        $this->assertSame(0, User::count());
    }

    // Requirement 11.2 / 3.2 / 3.4: missing Terms/Privacy acceptance is rejected with no records created.
    public function test_registration_without_accepting_terms_is_rejected(): void
    {
        Notification::fake();

        $response = $this->from('/register')->post('/register', $this->validPayload(['terms' => false]));
        $response->assertSessionHasErrors('terms');

        $payloadWithoutTerms = $this->validPayload();
        unset($payloadWithoutTerms['terms']);
        $response = $this->from('/register')->post('/register', $payloadWithoutTerms);
        $response->assertSessionHasErrors('terms');

        $this->assertSame(0, Business::count());
        $this->assertSame(0, User::count());
    }

    // 2.2: duplicate email is rejected and creates no new records
    public function test_registration_with_duplicate_email_is_rejected(): void
    {
        Notification::fake();

        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->from('/register')->post('/register', $this->validPayload());

        $response->assertSessionHasErrors('email');
        $this->assertSame(0, Business::count());
    }

    // 2.3: malformed emails are rejected and create no records
    public function test_registration_with_malformed_email_is_rejected(): void
    {
        Notification::fake();

        foreach (['not-an-email', 'missing-at.com', 'double@@at.com', '@no-local-part.com'] as $badEmail) {
            $response = $this->from('/register')->post('/register', $this->validPayload(['email' => $badEmail]));

            $response->assertSessionHasErrors('email');
        }

        $this->assertSame(0, Business::count());
        $this->assertSame(0, User::count());
    }

    // 2.4: too-short and unconfirmed passwords are rejected and create no records
    public function test_registration_with_weak_password_is_rejected(): void
    {
        Notification::fake();

        $response = $this->from('/register')->post('/register', $this->validPayload([
            'email' => 'weak-password@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertSame(0, Business::count());
    }

    public function test_registration_with_unconfirmed_password_is_rejected(): void
    {
        Notification::fake();

        $response = $this->from('/register')->post('/register', $this->validPayload([
            'email' => 'unconfirmed-password@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DoesNotMatch123!',
        ]));

        $response->assertSessionHasErrors('password');
        $this->assertSame(0, Business::count());
    }
}
