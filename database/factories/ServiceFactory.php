<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'duration_minutes' => 30,
            'price' => 100,
            'color' => '#2563ff',
            'is_active' => true,
            'confirmation_mode' => 'auto_confirmation',
        ];
    }

    public function requiresApproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmation_mode' => 'requires_approval',
        ]);
    }
}
