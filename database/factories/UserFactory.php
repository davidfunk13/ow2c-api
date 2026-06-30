<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sub' => fake()->unique()->uuid(),
            'battlenet_id' => fake()->unique()->numberBetween(100000000, 999999999),
            'battletag' => fake()->userName().'#'.fake()->numberBetween(1000, 9999),
            'remember_token' => Str::random(10),
        ];
    }
}
