<?php

namespace Database\Factories;

use App\Models\Map;
use App\Models\MapSubmap;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MapSubmap>
 */
class MapSubmapFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'map_id' => Map::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'image_url' => null,
        ];
    }
}
