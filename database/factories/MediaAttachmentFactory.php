<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Enums\ProcessingStatus;
use App\Models\MediaAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaAttachment>
 */
class MediaAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'attachable_id' => null,
            'attachable_type' => null,
            'media_type' => MediaType::GeneralScreenshot,
            'file_path' => 'screenshots/'.fake()->uuid().'.png',
            'original_filename' => fake()->word().'.png',
            'mime_type' => 'image/png',
            'file_size' => fake()->numberBetween(50000, 5000000),
            'processing_status' => ProcessingStatus::Pending,
        ];
    }
}
