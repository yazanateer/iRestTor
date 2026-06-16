<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'price' => 149,
        ]);

        Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'price' => 199,
        ]);
    }
}
