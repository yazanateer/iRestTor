<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '0501234567',
            'address' => fake()->address(),
            'timezone' => 'Asia/Jerusalem',
            'is_active' => true,
            'plan_id' => null,
        ];
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_id' => Plan::factory()->premium(),
        ]);
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_id' => null,
            'trial_ends_at' => now()->addDays(7),
        ]);
    }
}
