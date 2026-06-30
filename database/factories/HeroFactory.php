<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\SubRole;
use App\Models\Hero;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Hero>
 */
class HeroFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->firstName();
        $role = fake()->randomElement(Role::cases());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'role' => $role,
            'sub_role' => fake()->randomElement(SubRole::forRole($role)),
            'image_url' => null,
        ];
    }
}
