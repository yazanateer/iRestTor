<?php

namespace Tests\Unit;

use App\Models\Business;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BusinessTrialStateTest extends TestCase
{
    /**
     * Paid_Business: plan_id is not null, regardless of trial_ends_at.
     */
    public static function paidBusinessProvider(): array
    {
        return [
            'trial_ends_at in the future' => [now()->addDays(3)],
            'trial_ends_at in the past' => [now()->subDays(3)],
            'trial_ends_at is null' => [null],
        ];
    }

    #[DataProvider('paidBusinessProvider')]
    public function test_paid_business_is_paid_and_not_on_trial($trialEndsAt): void
    {
        $business = new Business([
            'plan_id' => 1,
            'trial_ends_at' => $trialEndsAt,
        ]);

        $this->assertTrue($business->isPaid());
        $this->assertFalse($business->onTrial());
        $this->assertFalse($business->trialExpired());
        $this->assertNull($business->trialDaysRemaining());
    }

    public function test_active_trial_business_is_on_trial(): void
    {
        $this->travelTo(now());

        $business = new Business([
            'plan_id' => null,
            'trial_ends_at' => now()->addDays(3),
        ]);

        $this->assertFalse($business->isPaid());
        $this->assertTrue($business->onTrial());
        $this->assertFalse($business->trialExpired());
        $this->assertSame(3, $business->trialDaysRemaining());
    }

    public function test_trial_ending_exactly_now_is_still_active(): void
    {
        $now = now()->startOfSecond();
        $this->travelTo($now);

        $business = new Business([
            'plan_id' => null,
            'trial_ends_at' => $now->copy(),
        ]);

        $this->assertTrue($business->onTrial());
        $this->assertFalse($business->trialExpired());
    }

    public function test_expired_trial_business_is_expired(): void
    {
        $this->travelTo(now());

        $business = new Business([
            'plan_id' => null,
            'trial_ends_at' => now()->subDays(2),
        ]);

        $this->assertFalse($business->isPaid());
        $this->assertFalse($business->onTrial());
        $this->assertTrue($business->trialExpired());
        $this->assertSame(0, $business->trialDaysRemaining());
    }

    public function test_business_with_null_trial_ends_at_and_null_plan_id_is_neither_paid_nor_on_trial(): void
    {
        $business = new Business([
            'plan_id' => null,
            'trial_ends_at' => null,
        ]);

        $this->assertFalse($business->isPaid());
        $this->assertFalse($business->onTrial());
        $this->assertFalse($business->trialExpired());
        $this->assertNull($business->trialDaysRemaining());
    }

    #[DataProvider('trialDaysRemainingProvider')]
    public function test_trial_days_remaining_across_the_trial_window(int $daysUntilExpiry, int $expectedDaysRemaining): void
    {
        $this->travelTo(now());

        $business = new Business([
            'plan_id' => null,
            'trial_ends_at' => now()->addDays($daysUntilExpiry),
        ]);

        $this->assertSame($expectedDaysRemaining, $business->trialDaysRemaining());
    }

    public static function trialDaysRemainingProvider(): array
    {
        return [
            '7 days remaining' => [7, 7],
            '1 day remaining' => [1, 1],
            'expires today' => [0, 0],
        ];
    }
}
