<?php

namespace Database\Factories;

use App\Models\CareerStatSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareerStatSnapshot>
 */
class CareerStatSnapshotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'hero_id' => null,
            'queue_type' => null,
            'stats_data' => [
                'eliminations' => fake()->numberBetween(0, 10000),
                'deaths' => fake()->numberBetween(0, 5000),
            ],
            'captured_at' => now(),
            'source' => 'manual',
        ];
    }
}
