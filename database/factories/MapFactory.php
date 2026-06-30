<?php

namespace Database\Factories;

use App\Enums\MapType;
use App\Models\Map;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Map>
 */
class MapFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'map_type' => fake()->randomElement(MapType::cases()),
            'image_url' => null,
        ];
    }
}
